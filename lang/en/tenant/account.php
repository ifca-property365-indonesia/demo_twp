<?php

// Modal profil tenant (View Profile) & pesan dari Tenant\AccountController.
return [
    'profile_picture'        => 'Profile picture',
    'change_picture'         => 'Change Picture',
    'picture_note'           => 'JPG, PNG or GIF, max 5 MB.',
    // berisi markup (dicetak dengan {!! !!})
    'picture_hint'           => 'New picture applied &mdash; click <strong>Save</strong> to keep it.',
    'crop_help'              => 'Drag the picture to position it inside the frame; use the slider or mouse wheel to zoom.',
    'use_photo'              => 'Use Photo',
    'tab_personal'           => 'Personal',
    'contact_name'           => 'Contact Name',
    'contact_name_note'      => 'Name of the person to contact for this account.',
    'email_note'             => 'This email is used to log in. If you change it, use the new email next time you log in.',
    'current_password'       => 'Current Password',
    'current_password_note'  => 'Required to change your email.',
    'current_password_wrong' => 'Current password is incorrect.',
    'email_invalid'          => 'Invalid email format.',
    'email_taken'            => 'This email is already used by another account.',
    'email_changed'          => 'Profile saved. Use :email next time you log in.',
    'handphone'              => 'Handphone',
    'phone_format'           => 'Format: 6221995500 | 021995500',
    'new_password'           => 'New Password',
    'confirm_password'       => 'Confirm Password',
    'password_mismatch'      => 'Password does not match',
    'only_image'             => 'Only JPG, PNG or GIF files are allowed.',
    'max_size'               => 'Maximum file size is 5 MB.',
    'picture_not_applied'    => 'Picture not applied yet',
    'picture_not_applied_text' => 'Click "Use Photo" first to keep the new picture, or Cancel to discard it.',

    // pesan controller
    'no_file'                => 'No file received (check upload size limit).',
    'upload_error'           => 'Upload error: :message',
    'max_size_server'        => 'Maximum file size is 5MB',
    'upload_failed'          => 'Sorry, there was an error uploading your file: :message',
];
