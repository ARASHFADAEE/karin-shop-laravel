<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\MediaFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaGallery extends Component
{
    use WithFileUploads, WithPagination;

    protected $layout = 'layouts.admin';

    // Upload properties
    public $files = [];
    public $uploadFolder = '';
    public $uploadDescription = '';
    public $uploadAltText = '';
    public $uploadTags = '';
    public $makePublic = false;

    // Filter properties
    public $search = '';
    public $filterType = '';
    public $filterFolder = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // UI properties
    public $showUploadModal = false;
    public $showDetailsModal = false;
    public $selectedFile = null;
    public $viewMode = 'grid'; // grid or list
    public $perPage = 24;

    // Edit properties
    public $editingFile = null;
    public $editName = '';
    public $editDescription = '';
    public $editAltText = '';
    public $editTags = '';
    public $editFolder = '';

    protected $rules = [
        'files.*' => 'required|file|max:10240', // 10MB max
        'uploadFolder' => 'nullable|string|max:255',
        'uploadDescription' => 'nullable|string|max:1000',
        'uploadAltText' => 'nullable|string|max:255',
        'uploadTags' => 'nullable|string',
    ];

    protected $listeners = [
        'fileSelected' => 'selectFile',
        'refreshGallery' => '$refresh'
    ];

    public function mount()
    {
        $this->resetPage();
    }

    public function render()
    {
        $mediaFiles = $this->getMediaFiles();
        $folders = MediaFile::getFolders();
        $stats = $this->getStats();

        return view('livewire.admin.media-gallery', [
            'mediaFiles' => $mediaFiles,
            'folders' => $folders,
            'stats' => $stats,
            'fileTypes' => $this->getFileTypes()
        ]);
    }

    public function getMediaFiles()
    {
        $query = MediaFile::search($this->search, [
            'type' => $this->filterType,
            'folder' => $this->filterFolder,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ]);

        return $query->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
    }

    public function getStats()
    {
        return [
            'total_files' => MediaFile::active()->count(),
            'total_size' => MediaFile::active()->sum('size'),
            'images_count' => MediaFile::active()->ofType('image')->count(),
            'videos_count' => MediaFile::active()->ofType('video')->count(),
            'documents_count' => MediaFile::active()->ofType('document')->count(),
        ];
    }

    public function getFileTypes()
    {
        return [
            '' => 'همه فایل‌ها',
            'image' => 'تصاویر',
            'video' => 'ویدیوها',
            'audio' => 'صوتی',
            'document' => 'اسناد',
            'archive' => 'آرشیو',
            'other' => 'سایر'
        ];
    }

    public function openUploadModal()
    {
        $this->showUploadModal = true;
        $this->resetUploadForm();
    }

    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->resetUploadForm();
    }

    public function resetUploadForm()
    {
        $this->files = [];
        $this->uploadFolder = '';
        $this->uploadDescription = '';
        $this->uploadAltText = '';
        $this->uploadTags = '';
        $this->makePublic = false;
    }

    public function uploadFiles()
    {
        $this->validate();

        if (empty($this->files)) {
            $this->addError('files', 'لطفاً حداقل یک فایل انتخاب کنید.');
            return;
        }

        $uploadedFiles = [];
        $tags = $this->uploadTags ? explode(',', $this->uploadTags) : null;

        foreach ($this->files as $file) {
            try {
                $mediaFile = MediaFile::createFromUpload(
                    $file,
                    Auth::user(),
                    $this->uploadFolder ?: null,
                    [
                        'description' => $this->uploadDescription,
                        'alt_text' => $this->uploadAltText,
                        'tags' => $tags,
                        'public' => $this->makePublic
                    ]
                );
                
                $uploadedFiles[] = $mediaFile;
            } catch (\Exception $e) {
                $this->addError('files', 'خطا در آپلود فایل: ' . $e->getMessage());
                return;
            }
        }

        $this->closeUploadModal();
        $this->dispatch('refreshGallery');
        
        session()->flash('message', count($uploadedFiles) . ' فایل با موفقیت آپلود شد.');
    }

    public function selectFile($fileId)
    {
        $this->selectedFile = MediaFile::find($fileId);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedFile = null;
    }

    public function downloadFile($fileId)
    {
        $file = MediaFile::find($fileId);
        if ($file) {
            $file->incrementDownloadCount();
            return response()->download(
                Storage::disk($file->disk)->path($file->file_path),
                $file->original_name
            );
        }
    }

    public function deleteFile($fileId)
    {
        $file = MediaFile::find($fileId);
        if ($file) {
            $file->deleteFile();
            $this->closeDetailsModal();
            $this->dispatch('refreshGallery');
            session()->flash('message', 'فایل با موفقیت حذف شد.');
        }
    }

    public function editFile($fileId)
    {
        $this->editingFile = MediaFile::find($fileId);
        if ($this->editingFile) {
            $this->editName = $this->editingFile->name;
            $this->editDescription = $this->editingFile->description ?? '';
            $this->editAltText = $this->editingFile->alt_text ?? '';
            $this->editTags = $this->editingFile->tags ? implode(', ', $this->editingFile->tags) : '';
            $this->editFolder = $this->editingFile->folder ?? '';
        }
    }

    public function updateFile()
    {
        if (!$this->editingFile) {
            return;
        }

        $this->validate([
            'editName' => 'required|string|max:255',
            'editDescription' => 'nullable|string|max:1000',
            'editAltText' => 'nullable|string|max:255',
            'editTags' => 'nullable|string',
            'editFolder' => 'nullable|string|max:255',
        ]);

        $tags = $this->editTags ? array_map('trim', explode(',', $this->editTags)) : null;

        $this->editingFile->update([
            'name' => $this->editName,
            'description' => $this->editDescription,
            'alt_text' => $this->editAltText,
            'tags' => $tags,
            'folder' => $this->editFolder ?: null,
        ]);

        $this->editingFile = null;
        $this->dispatch('refreshGallery');
        session()->flash('message', 'اطلاعات فایل به‌روزرسانی شد.');
    }

    public function cancelEdit()
    {
        $this->editingFile = null;
    }

    public function copyFileUrl($fileId)
    {
        $file = MediaFile::find($fileId);
        if ($file) {
            $this->dispatch('copyToClipboard', $file->getFullUrl());
        }
    }

    public function togglePublic($fileId)
    {
        $file = MediaFile::find($fileId);
        if ($file) {
            $file->update(['is_public' => !$file->is_public]);
            $this->dispatch('refreshGallery');
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterType = '';
        $this->filterFolder = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedFilterFolder()
    {
        $this->resetPage();
    }

    public function setSortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc';
        }
        $this->resetPage();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
        $this->resetPage();
    }
}