<?php
return [
    'common' => [
        'actions' => [
            'back' => 'عودة',
            'cancel' => 'إلغاء',
            'delete' => 'حذف',
            'edit' => 'تعديل',
            'filter' => 'تصفية',
        ],
        'filters' => [
            'all_statuses' => 'كل الحالات',
        ],
        'table' => [
            'no_records' => 'لا توجد سجلات',
        ],
        'time' => [
            'minutes_short' => 'د',
        ],
        'forms' => [
            'characters' => 'حرف',
        ],
    ],
    'admin' => [
        'content' => [
            'status' => [
                'draft' => 'مسودة',
                'published' => 'منشور',
                'archived' => 'مؤرشف',
                'unknown' => 'غير معروف',
            ],
            'index' => [
                'title' => 'إدارة المحتوى التوعوي',
                'subtitle' => 'إضافة وتحرير المحتوى التوعوي في المنصة',
                'buttons' => [
                    'create' => 'إضافة محتوى جديد',
                ],
                'filters' => [
                    'search_placeholder' => 'البحث في المحتوى...',
                    'type_placeholder' => 'النوع (article/video/pdf)',
                ],
                'table' => [
                    'headers' => [
                        'title' => 'عنوان المحتوى',
                        'type' => 'النوع',
                        'points' => 'النقاط',
                        'duration' => 'المدة',
                        'status' => 'الحالة',
                        'actions' => 'الإجراءات',
                    ],
                ],
                'confirm_delete' => 'حذف هذا المحتوى؟',
            ],
            'create' => [
                'title' => 'إنشاء محتوى',
                'subtitle' => 'أضف مواد التوعية بالمؤسسة',
                'buttons' => [
                    'submit' => 'نشر المحتوى',
                ],
                'console' => [
                    'error_code' => 'رمز الخطأ',
                    'creation_errors' => 'أخطاء إنشاء المحتوى',
                    'dev_details_title' => 'تفاصيل تقنية (للتشخيص المحلي فقط)',
                    'type_label' => 'النوع:',
                    'message_label' => 'الرسالة:',
                    'sqlstate_label' => 'SQLSTATE:',
                    'driver_label' => 'Driver:',
                    'time_label' => 'الوقت:',
                ],
                'form' => [
                    'title_label' => 'عنوان المحتوى',
                    'title_placeholder' => 'مثال: أهم ممارسات أمان كلمة المرور',
                    'category_label' => 'الفئة',
                    'category_aria' => 'اختيار الفئة',
                    'category_placeholder' => 'اختر الفئة',
                    'default_categories' => [
                        'basic_security' => 'الحماية الأساسية',
                        'email_security' => 'أمان البريد الإلكتروني',
                        'mobile_security' => 'حماية الأجهزة المحمولة',
                        'password_management' => 'إدارة كلمات المرور',
                        'network_security' => 'أمان الشبكات',
                        'cloud_storage' => 'التخزين السحابي',
                    ],
                    'type_label' => 'نوع المحتوى',
                    'type_aria' => 'نوع المحتوى',
                    'type_options' => [
                        'article' => 'محتوى نصي',
                        'video' => 'محتوى فيديو',
                    ],
                    'type_help' => 'اختر النوع المناسب ليتم عرض الحقول ذات الصلة فقط.',
                    'article_body_label' => 'المحتوى النصي',
                    'article_body_placeholder' => 'اكتب المحتوى هنا... (يدعم النصوص الطويلة)',
                    'article_body_hint' => 'سيتم الحفظ في الحقل `body` ضمن جدول المحتوى.',
                    'media_url_label' => 'رابط الوسائط (media_url)',
                    'media_url_placeholder' => 'أدخل رابط الوسائط: YouTube, Vimeo, أو ملف خارجي',
                    'media_url_hint' => 'مثال: https://www.youtube.com/watch?v=xxxx — سيتم الحفظ في `media_url`.',
                    'description_label' => 'وصف المحتوى',
                    'description_placeholder' => 'اكتب وصفًا مختصرًا للمحتوى...',
                    'difficulty_label' => 'مستوى الصعوبة',
                    'difficulty_placeholder' => 'اختر المستوى',
                    'difficulty_options' => [
                        'beginner' => 'مبتدئ',
                        'intermediate' => 'متوسط',
                        'advanced' => 'متقدم',
                    ],
                    'duration_label' => 'وقت القراءة/المشاهدة المتوقع (بالدقائق)',
                    'duration_placeholder' => 'مثال: 5',
                    'points_label' => 'نقاط المكافأة',
                    'status_label' => 'حالة النشر',
                    'thumbnail_label' => 'رابط الصورة المصغرة (thumbnail_url)',
                    'thumbnail_placeholder' => 'https://...',
                    'featured_label' => 'محتوى مميز',
                ],
                'alerts' => [
                    'body_required' => 'يرجى كتابة المحتوى النصي.',
                    'media_required' => 'يرجى إدراج رابط الوسائط المناسب لهذا النوع.',
                ],
            ],
        ],
    ],
];
