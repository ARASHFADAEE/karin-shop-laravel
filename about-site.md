Table users {
  id bigint [pk, increment]
  name varchar(100)
  email varchar(150) [unique]
  password varchar(255)
  phone varchar(20)
  role varchar(20) // admin, customer
  created_at timestamp
  updated_at timestamp
}

Table categories {
  id bigint [pk, increment]
  parent_id bigint [ref: > categories.id]
  name varchar(150)
  slug varchar(150) [unique]
  description text
  created_at timestamp
  updated_at timestamp
}

Table products {
  id bigint [pk, increment]
  category_id bigint [ref: > categories.id]
  name varchar(200)
  slug varchar(200) [unique]
  description text
  price decimal(12,2)
  stock int
  sku varchar(100) [unique]
  status varchar(20) // active, draft, out_of_stock
  created_at timestamp
  updated_at timestamp
}

Table product_featured_images {
  id bigint [pk, increment]
  product_id bigint [ref: > products.id]
  image_url varchar(255)
  created_at timestamp
}

Table product_images {
  id bigint [pk, increment]
  product_id bigint [ref: > products.id]
  image_url varchar(255)
  sort_order int
  created_at timestamp
}

Table product_attributes {
  id bigint [pk, increment]
  product_id bigint [ref: > products.id]
  attribute_name varchar(100)
  attribute_value varchar(100)
}

Table carts {
  id bigint [pk, increment]
  user_id bigint [ref: > users.id]
  created_at timestamp
  updated_at timestamp
}

Table cart_items {
  id bigint [pk, increment]
  cart_id bigint [ref: > carts.id]
  product_id bigint [ref: > products.id]
  quantity int
  price decimal(12,2)
}

Table orders {
  id bigint [pk, increment]
  user_id bigint [ref: > users.id]
  status varchar(20) // pending, paid, shipped, completed, canceled
  total_amount decimal(12,2)
  shipping_address text
  payment_method varchar(50)
  created_at timestamp
  updated_at timestamp
}

Table order_items {
  id bigint [pk, increment]
  order_id bigint [ref: > orders.id]
  product_id bigint [ref: > products.id]
  quantity int
  price decimal(12,2)
}

Table payments {
  id bigint [pk, increment]
  order_id bigint [ref: > orders.id]
  amount decimal(12,2)
  payment_method varchar(50)
  status varchar(20) // pending, success, failed
  transaction_id varchar(100)
  created_at timestamp
}

Table reviews {
  id bigint [pk, increment]
  product_id bigint [ref: > products.id]
  user_id bigint [ref: > users.id]
  rating int
  comment text
  created_at timestamp
}

Table wishlists {
  id bigint [pk, increment]
  user_id bigint [ref: > users.id]
  product_id bigint [ref: > products.id]
  created_at timestamp
}

Table coupons {
  id bigint [pk, increment]
  code varchar(50) [unique]
  discount_type varchar(20) // percentage, fixed
  discount_value decimal(12,2)
  start_date date
  end_date date
  usage_limit int
  created_at timestamp
}

Table coupon_user {
  id bigint [pk, increment]
  coupon_id bigint [ref: > coupons.id]
  user_id bigint [ref: > users.id]
  used_at timestamp
}

Table seo_meta {
  id bigint [pk, increment]
  metaable_id bigint
  metaable_type varchar(100) // Product, Category, Page, Blog...
  meta_title varchar(255)
  meta_description text
  meta_keywords text
  canonical_url varchar(255)
  og_title varchar(255)
  og_description text
  og_image varchar(255)
  twitter_card varchar(50)
  robots varchar(50) // index, noindex, follow, nofollow
  created_at timestamp
  updated_at timestamp
}


میخوام که دیاگرام بالا رو عمیقا برسی کنی و مایگریشن ها و رابطه های بین تیبل ها و مدل هارو پیاده سازی کنی 

اگر نکته ای هم بود باهام مطرح کن
