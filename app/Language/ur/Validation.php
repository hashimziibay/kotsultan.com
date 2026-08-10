<?php

declare(strict_types=1);

/**
 * Urdu translations of the CodeIgniter 4 Validation messages.
 *
 * Ensures form validation errors render in Urdu when the active locale is 'ur'.
 * English messages come from the framework's system/Language/en/Validation.php.
 * Placeholders ({field}, {param}, {0}) behave exactly like the English originals.
 */

return [
    // Core Messages
    'noRuleSets'      => 'Validation کی ترتیب میں کوئی اصول مقرر نہیں ہے۔',
    'ruleNotFound'    => '"{0}" ایک درست اصول نہیں ہے۔',
    'groupNotFound'   => '"{0}" ایک درست توثیقی اصولوں کا گروپ نہیں ہے۔',
    'groupNotArray'   => '"{0}" اصول گروپ ایک array ہونا چاہیے۔',
    'invalidTemplate' => '"{0}" ایک درست توثیقی ٹیمپلیٹ نہیں ہے۔',

    // Rule Messages
    'alpha'                 => '{field} خانے میں صرف حروفِ تہجی ہوسکتے ہیں۔',
    'alpha_dash'            => '{field} خانے میں صرف حروفِ تہجی، اعداد، انڈر سکور اور ڈیش ہوسکتے ہیں۔',
    'alpha_numeric'         => '{field} خانے میں صرف حروفِ تہجی اور اعداد ہوسکتے ہیں۔',
    'alpha_numeric_punct'   => '{field} خانے میں صرف حروفِ تہجی، اعداد، خالی جگہ اور ~ ! # $ % & * - _ + = | : . ہوسکتے ہیں۔',
    'alpha_numeric_space'   => '{field} خانے میں صرف حروفِ تہجی، اعداد اور خالی جگہ ہوسکتی ہے۔',
    'alpha_space'           => '{field} خانے میں صرف حروفِ تہجی اور خالی جگہ ہوسکتی ہے۔',
    'decimal'               => '{field} خانے میں اعشاریہ نمبر ہونا ضروری ہے۔',
    'differs'               => '{field} خانہ {param} خانے سے مختلف ہونا چاہیے۔',
    'equals'                => '{field} خانہ بالکل یہ ہونا چاہیے: {param}۔',
    'exact_length'          => '{field} خانے کی لمبائی بالکل {param} حروف ہونی چاہیے۔',
    'field_exists'          => '{field} خانہ موجود ہونا چاہیے۔',
    'greater_than'          => '{field} خانے میں {param} سے بڑا نمبر ہونا ضروری ہے۔',
    'greater_than_equal_to' => '{field} خانے میں {param} سے بڑا یا برابر نمبر ہونا ضروری ہے۔',
    'hex'                   => '{field} خانے میں صرف ہیکسا ڈیسیمل حروف ہوسکتے ہیں۔',
    'in_list'               => '{field} خانہ ان میں سے ایک ہونا چاہیے: {param}۔',
    'integer'               => '{field} خانے میں عدد صحیح ہونا ضروری ہے۔',
    'is_natural'            => '{field} خانے میں صرف اعداد ہوسکتے ہیں۔',
    'is_natural_no_zero'    => '{field} خانے میں صرف اعداد ہوسکتے ہیں اور صفر سے بڑا ہونا چاہیے۔',
    'is_not_unique'         => '{field} خانے میں ڈیٹا بیس میں پہلے سے موجود قدر ہونی چاہیے۔',
    'is_unique'             => '{field} خانے میں منفرد قدر ہونی چاہیے۔',
    'less_than'             => '{field} خانے میں {param} سے چھوٹا نمبر ہونا ضروری ہے۔',
    'less_than_equal_to'    => '{field} خانے میں {param} سے چھوٹا یا برابر نمبر ہونا ضروری ہے۔',
    'matches'               => '{field} خانہ {param} خانے سے مماثل نہیں ہے۔',
    'max_length'            => '{field} خانے کی لمبائی {param} حروف سے زیادہ نہیں ہوسکتی۔',
    'min_length'            => '{field} خانے کی لمبائی کم از کم {param} حروف ہونی چاہیے۔',
    'not_equals'            => '{field} خانہ یہ نہیں ہوسکتا: {param}۔',
    'not_in_list'           => '{field} خانہ ان میں سے ایک نہیں ہونا چاہیے: {param}۔',
    'numeric'               => '{field} خانے میں صرف اعداد ہونے چاہئیں۔',
    'regex_match'           => '{field} خانہ درست فارمیٹ میں نہیں ہے۔',
    'required'              => '{field} خانہ درکار ہے۔',
    'required_with'         => '{field} خانہ درکار ہے جب {param} موجود ہو۔',
    'required_without'      => '{field} خانہ درکار ہے جب {param} موجود نہ ہو۔',
    'string'                => '{field} خانہ ایک درست سٹرنگ ہونا چاہیے۔',
    'timezone'              => '{field} خانہ ایک درست ٹائم زون ہونا چاہیے۔',
    'valid_base64'          => '{field} خانہ ایک درست base64 سٹرنگ ہونا چاہیے۔',
    'valid_email'           => '{field} خانے میں درست ای میل ایڈریس ہونا چاہیے۔',
    'valid_emails'          => '{field} خانے میں تمام ای میل ایڈریس درست ہونے چاہئیں۔',
    'valid_ip'              => '{field} خانے میں درست IP ہونا چاہیے۔',
    'valid_url'             => '{field} خانے میں درست URL ہونا چاہیے۔',
    'valid_url_strict'      => '{field} خانے میں درست URL ہونا چاہیے۔',
    'valid_date'            => '{field} خانے میں درست تاریخ ہونی چاہیے۔',
    'valid_json'            => '{field} خانے میں درست json ہونا چاہیے۔',

    // Credit Cards
    'valid_cc_number' => '{field} ایک درست کریڈٹ کارڈ نمبر نہیں لگتا۔',

    // Files
    'uploaded' => '{field} ایک درست اپ لوڈ شدہ فائل نہیں ہے۔',
    'max_size' => '{field} فائل بہت بڑی ہے۔',
    'is_image' => '{field} ایک درست، اپ لوڈ شدہ تصویری فائل نہیں ہے۔',
    'mime_in'  => '{field} کا درست mime ٹائپ نہیں ہے۔',
    'ext_in'   => '{field} کی درست فائل ایکسٹینشن نہیں ہے۔',
    'max_dims' => '{field} یا تو تصویر نہیں ہے، یا بہت چوڑی/لمبی ہے۔',
    'min_dims' => '{field} یا تو تصویر نہیں ہے، یا کافی چوڑی/لمبی نہیں ہے۔',
];
