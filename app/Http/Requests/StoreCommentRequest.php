<?php

namespace App\Http\Requests;

use App\Enums\CommentStatus;
use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Comment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:2', 'max:'.config('blog.comments.max_length', 1000)],
            'parent_id' => ['nullable', 'integer', Rule::exists('comments', 'id')],
        ];
    }

    /**
     * Validate the request.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->filled('parent_id')) {
                $parent = Comment::withTrashed()->find($this->input('parent_id'));

                if (! $parent) {
                    return;
                }

                $post = $this->route('post');

                if ($parent->post_id !== $post?->id
                    || $parent->trashed()
                    || $parent->status !== CommentStatus::Approved->value
                    || $parent->parent_id !== null) {
                    $validator->errors()->add('parent_id', 'You can only reply to an approved top-level comment.');
                }
            }
        });
    }
}
