<?php

namespace App\Http\Requests\Resumes;

/**
 * The same document rules as the signed-in editor, so a draft that passes here
 * can be saved to a new CV unchanged. Only active templates are allowed, as
 * there is no existing CV to keep a retired one for.
 */
class SaveGuestResumeRequest extends UpdateResumeRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
