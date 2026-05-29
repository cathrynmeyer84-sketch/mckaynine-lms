<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassType extends Model
{
    protected $fillable = [
        'name', 'description', 'duration_type', 'term_weeks',
        'billing_period', 'has_structured_content', 'has_grading',
        'rosette_image_path', 'enrolment_mode', 'completion_message',
        'slug', 'tagline', 'hero_heading', 'about', 'general_schedule', 'cost_from', 'cost_notes',
        'image_path', 'image_mobile_path', 'promo_video_url', 'gallery_images', 'documents', 'fees_image_path', 'fees_image_mobile_path',
        'testimonial_text', 'testimonial_name', 'testimonial_photo_path',
        'page_template', 'is_entry_class', 'info_page_enabled', 'is_public', 'individual_class_pages',
        'color_theme', 'hero_overlay_color',
        'trust_strap', 'helps_with', 'age_requirements', 'what_to_bring', 'how_to_join_steps', 'joining_notes',
        'cta_type',
        'prerequisite_class_type_ids',
        'io_prod_code',
        'monthly_fee_per_dog',
        'course_price',
    ];

    protected $casts = [
        'has_structured_content'       => 'boolean',
        'has_grading'                  => 'boolean',
        'is_entry_class'               => 'boolean',
        'term_weeks'                   => 'integer',
        'gallery_images'               => 'array',
        'documents'                    => 'array',
        'info_page_enabled'            => 'boolean',
        'is_public'                    => 'boolean',
        'individual_class_pages'       => 'boolean',
        'cost_from'                    => 'decimal:2',
        'monthly_fee_per_dog'          => 'decimal:2',
        'course_price'                 => 'decimal:2',
        'prerequisite_class_type_ids'  => 'array',
    ];

    public function palette(): array
    {
        $palettes = [
            'forest' => [
                'primary'      => '#365236',
                'secondary'    => '#446C42',
                'accent'       => '#647653',
                'left_col'     => '#C8DFD6',
                'right_col'    => '#D6C2B5',
                'heading'      => '#365236',
                'step_badge'   => '#3569BD',
                'btn_bg'       => '#9BC6B5',
                'btn_text'     => '#365236',
                'footer_bg'    => '#4C7AC6',
            ],
            'ocean' => [
                'primary'      => '#001d6d',
                'secondary'    => '#3964b0',
                'accent'       => '#3964b0',
                'left_col'     => '#d8e3f5',
                'right_col'    => '#eaecf0',
                'heading'      => '#001d6d',
                'step_badge'   => '#3964b0',
                'btn_bg'       => '#b0c8e8',
                'btn_text'     => '#001d6d',
                'footer_bg'    => '#4C7AC6',
            ],
            'slate' => [
                'primary'      => '#3d3530',
                'secondary'    => '#6b6560',
                'accent'       => '#c4714a',
                'left_col'     => '#f0ece8',
                'right_col'    => '#e8e2dc',
                'heading'      => '#3d3530',
                'step_badge'   => '#c4714a',
                'btn_bg'       => '#f0c4b0',
                'btn_text'     => '#3d3530',
                'footer_bg'    => '#6b6560',
            ],
        ];
        return $palettes[$this->color_theme ?? 'forest'] ?? $palettes['forest'];
    }

    public function weeks()
    {
        return $this->hasMany(ClassTypeWeek::class)->orderBy('week_number');
    }

    public function gradingExercises()
    {
        return $this->hasMany(GradingExercise::class)->orderBy('sort_order');
    }

    public function classes()
    {
        return $this->hasMany(DogClass::class, 'class_type_id');
    }

    public function availableClasses()
    {
        return $this->classes()
            ->where('end_date', '>=', now())
            ->whereRaw('max_capacity > (SELECT COUNT(*) FROM enrolments WHERE enrolments.class_id = classes.id AND enrolments.status = ?)', ['confirmed'])
            ->with('instructors')
            ->orderBy('start_date');
    }

    public function getDurationLabelAttribute(): string
    {
        if ($this->duration_type === 'term') {
            return $this->term_weeks ? "{$this->term_weeks}-week term" : 'Term';
        }
        return ucfirst($this->billing_period ?? 'Ongoing');
    }
}
