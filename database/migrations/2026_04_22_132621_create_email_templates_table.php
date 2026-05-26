<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('subject');
            $table->text('body');
            $table->text('available_placeholders')->nullable();
            $table->timestamps();
        });

        // Seed default templates
        $templates = [
            [
                'key'                    => 'enrolment_confirmed',
                'name'                   => 'Enrolment Confirmed',
                'subject'                => 'Welcome to McKaynine — your enrolment is confirmed!',
                'body'                   => "Hi {handler_name},\n\nGreat news! Your enrolment for {dog_name} in {class_name} has been confirmed.\n\nHere's what to do next:\n\n1. Set up your account password: {password_setup_link}\n\n2. Download our app (PWA): {pwa_link}\n   Once you're in, please upload a photo of {dog_name} to complete your profile.\n\n3. Enable push notifications in the app so you don't miss any updates about your class.\n\nIf you have any questions, please don't hesitate to get in touch.\n\nSee you at class!\nThe McKaynine Team",
                'available_placeholders' => '{handler_name}, {dog_name}, {class_name}, {password_setup_link}, {pwa_link}',
            ],
            [
                'key'                    => 'vet_clearance_request',
                'name'                   => 'Vet Clearance Certificate Request',
                'subject'                => 'Action required: Vet clearance certificate for {dog_name}',
                'body'                   => "Hi {handler_name},\n\nThank you for enrolling {dog_name} in {class_name}.\n\nBefore we can confirm your spot, we require a vet clearance certificate for {dog_name}.\n\nPlease follow these steps:\n\n1. Download the vet clearance form: {vet_clearance_pdf_link}\n2. Take it to your vet to complete and sign.\n3. Upload the completed certificate here: {upload_link}\n\nOnce we've received and reviewed the certificate, we'll confirm your enrolment.\n\nIf you have any questions, please get in touch.\n\nKind regards,\nThe McKaynine Team",
                'available_placeholders' => '{handler_name}, {dog_name}, {class_name}, {vet_clearance_pdf_link}, {upload_link}',
            ],
            [
                'key'                    => 'enrolment_rejected',
                'name'                   => 'Enrolment Not Accepted',
                'subject'                => 'Update on your enrolment for {dog_name}',
                'body'                   => "Hi {handler_name},\n\nThank you for your interest in enrolling {dog_name} in {class_name}.\n\nUnfortunately, after reviewing your application, we are unable to accept this enrolment at this time.\n\n{reason}\n\nWe appreciate your understanding. If you'd like to discuss this further or explore other options, please feel free to contact us.\n\nKind regards,\nThe McKaynine Team",
                'available_placeholders' => '{handler_name}, {dog_name}, {class_name}, {reason}',
            ],
        ];

        foreach ($templates as $template) {
            \DB::table('email_templates')->insert(array_merge($template, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
