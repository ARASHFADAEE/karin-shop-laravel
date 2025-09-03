<?php

namespace App\Services;

use App\Models\Setting;
use SoapClient;
use SoapFault;
use Exception;

class MelliPayamakService
{
    private $username;
    private $password;
    private $senderNumber;
    private $soapClient;

    public function __construct()
    {
        $settings = Setting::first();
        
        $this->username = $settings->melli_payamak_username ?? '';
        $this->password = $settings->melli_payamak_password ?? '';
        $this->senderNumber = $settings->melli_payamak_sender_number ?? '';
        
        // ایجاد SoapClient
        try {
            $this->soapClient = new SoapClient(
                "http://api.payamak-panel.com/post/Send.asmx?wsdl",
                array("encoding" => "UTF-8", "trace" => 1)
            );
        } catch (Exception $e) {
            throw new Exception('خطا در اتصال به سرویس ملی پیامک: ' . $e->getMessage());
        }
    }

    /**
     * ارسال پیامک ساده
     *
     * @param string|array $to شماره موبایل یا آرایه‌ای از شماره‌ها
     * @param string $text متن پیامک
     * @return array نتیجه ارسال
     */
    public function sendSMS($to, $text)
    {
        if (empty($this->username) || empty($this->password)) {
            return [
                'success' => false,
                'message' => 'تنظیمات ملی پیامک کامل نشده است'
            ];
        }

        // اگر شماره‌ها آرایه است، به رشته تبدیل شود
        if (is_array($to)) {
            $to = implode(',', $to);
        }

        $data = [
            "username" => $this->username,
            "password" => $this->password,
            "text" => $text,
            "to" => $to,
            "from" => $this->senderNumber,
            "isflash" => false
        ];

        try {
            $result = $this->soapClient->SendSimpleSMS2($data);
            
            return [
                'success' => true,
                'result' => $result->SendSimpleSMS2Result,
                'message' => 'پیامک با موفقیت ارسال شد'
            ];
        } catch (SoapFault $e) {
            return [
                'success' => false,
                'message' => 'خطا در ارسال پیامک: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ارسال پیامک با پترن
     *
     * @param string|array $to شماره موبایل یا آرایه‌ای از شماره‌ها
     * @param string $patternCode کد پترن
     * @param array $args آرگومان‌های پترن
     * @return array نتیجه ارسال
     */
    public function sendPatternSMS($to, $patternCode, $args = [])
    {
        if (empty($this->username) || empty($this->password)) {
            return [
                'success' => false,
                'message' => 'تنظیمات ملی پیامک کامل نشده است'
            ];
        }

        // اگر شماره‌ها آرایه است، به رشته تبدیل شود
        if (is_array($to)) {
            $to = implode(',', $to);
        }

        // ساخت متن با آرگومان‌ها
        $text = $patternCode;
        if (!empty($args)) {
            $text .= ';' . implode(';', $args);
        }

        $data = [
            "username" => $this->username,
            "password" => $this->password,
            "text" => $text,
            "to" => $to,
            "from" => $this->senderNumber,
            "isflash" => false
        ];

        try {
            $result = $this->soapClient->SendByBaseNumber3($data);
            
            return [
                'success' => true,
                'result' => $result->SendByBaseNumber3Result,
                'message' => 'پیامک با موفقیت ارسال شد'
            ];
        } catch (SoapFault $e) {
            return [
                'success' => false,
                'message' => 'خطا در ارسال پیامک: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ارسال کد ورود یکبار مصرف
     *
     * @param string $mobile شماره موبایل
     * @param string $code کد ورود
     * @return array نتیجه ارسال
     */
    public function sendLoginCode($mobile, $code)
    {
        $settings = Setting::first();
        $patternCode = $settings->sms_pattern_login_code ?? '';
        
        if (empty($patternCode)) {
            return $this->sendSMS($mobile, "کد یک بار مصرف شما {$code} میباشد");
        }
        
        return $this->sendPatternSMS($mobile, $patternCode, [$code]);
    }

    /**
     * ارسال پیامک ثبت سفارش
     *
     * @param string $mobile شماره موبایل
     * @param string $customerName نام مشتری
     * @param string $orderNumber شماره سفارش
     * @param string $items آیتم‌های سفارش
     * @param string $amount مبلغ سفارش
     * @return array نتیجه ارسال
     */
    public function sendOrderCreated($mobile, $customerName, $orderNumber, $items, $amount)
    {
        $settings = Setting::first();
        $patternCode = $settings->sms_pattern_order_created ?? '';
        
        if (empty($patternCode)) {
            $text = "سلام {$customerName} عزیز\nسفارش {$orderNumber} دریافت شد و هم اکنون در وضعیت در انتظار پرداخت می‌باشد.\nآیتم های سفارش: {$items}\nمبلغ سفارش: {$amount}";
            return $this->sendSMS($mobile, $text);
        }
        
        return $this->sendPatternSMS($mobile, $patternCode, [$customerName, $orderNumber, $items, $amount]);
    }

    /**
     * ارسال پیامک تغییر وضعیت سفارش
     *
     * @param string $mobile شماره موبایل
     * @param string $customerName نام مشتری
     * @param string $orderNumber شماره سفارش
     * @param string $status وضعیت جدید
     * @return array نتیجه ارسال
     */
    public function sendOrderStatusChanged($mobile, $customerName, $orderNumber, $status)
    {
        $settings = Setting::first();
        $statusText = $this->getStatusText($status);
        
        $patternCode = '';
        switch ($status) {
            case 'processing':
                $patternCode = $settings->sms_pattern_order_processing ?? '';
                break;
            case 'shipped':
                $patternCode = $settings->sms_pattern_order_shipped ?? '';
                break;
            case 'delivered':
                $patternCode = $settings->sms_pattern_order_delivered ?? '';
                break;
            case 'cancelled':
                $patternCode = $settings->sms_pattern_order_cancelled ?? '';
                break;
        }
        
        if (empty($patternCode)) {
            $text = "سلام {$customerName} عزیز\nوضعیت سفارش {$orderNumber} به {$statusText} تغییر یافت.";
            return $this->sendSMS($mobile, $text);
        }
        
        return $this->sendPatternSMS($mobile, $patternCode, [$customerName, $orderNumber, $statusText]);
    }

    /**
     * ارسال پیامک به ادمین برای سفارش جدید
     *
     * @param string $adminMobile شماره موبایل ادمین
     * @param string $orderNumber شماره سفارش
     * @param string $customerName نام مشتری
     * @param string $amount مبلغ سفارش
     * @return array نتیجه ارسال
     */
    public function sendAdminNewOrder($adminMobile, $orderNumber, $customerName, $amount)
    {
        $settings = Setting::first();
        $patternCode = $settings->sms_pattern_admin_new_order ?? '';
        
        if (empty($patternCode)) {
            $text = "سفارش جدید\nشماره: {$orderNumber}\nمشتری: {$customerName}\nمبلغ: {$amount}";
            return $this->sendSMS($adminMobile, $text);
        }
        
        return $this->sendPatternSMS($adminMobile, $patternCode, [$orderNumber, $customerName, $amount]);
    }

    /**
     * تبدیل وضعیت به متن فارسی
     *
     * @param string $status
     * @return string
     */
    private function getStatusText($status)
    {
        $statuses = [
            'pending' => 'در انتظار پردازش',
            'processing' => 'در حال پردازش',
            'shipped' => 'ارسال شده',
            'delivered' => 'تحویل داده شده',
            'cancelled' => 'لغو شده'
        ];
        
        return $statuses[$status] ?? $status;
    }

    /**
     * بررسی اعتبار تنظیمات
     *
     * @return bool
     */
    public function isConfigured()
    {
        return !empty($this->username) && !empty($this->password) && !empty($this->senderNumber);
    }

    /**
     * دریافت اعتبار باقیمانده
     *
     * @return array
     */
    public function getCredit()
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'تنظیمات ملی پیامک کامل نشده است'
            ];
        }

        try {
            $data = [
                "username" => $this->username,
                "password" => $this->password
            ];
            
            $result = $this->soapClient->GetCredit($data);
            
            return [
                'success' => true,
                'credit' => $result->GetCreditResult,
                'message' => 'اعتبار با موفقیت دریافت شد'
            ];
        } catch (SoapFault $e) {
            return [
                'success' => false,
                'message' => 'خطا در دریافت اعتبار: ' . $e->getMessage()
            ];
        }
    }
}