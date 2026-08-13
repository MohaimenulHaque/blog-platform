@php
    $authUser = auth()->user();
    $isOwn = $authUser !== null && $authUser->id === $comment->user_id;
    $isManager = $authUser !== null && $authUser->hasAnyRole(['admin', 'editor']);
    $postUrl = route('posts.comments.store', $comment->post);
@endphp

<article
    id="comment-{{ $comment->id }}"
    class="flex gap-4"
    x-data="commentItem({
        id: {{ $comment->id }},
        body: @js($comment->body),
        liked: {{ $comment->likes->isNotEmpty() ? 'true' : 'false' }},
        likeCount: {{ $comment->likes_count }},
        auth: {{ $authUser ? 'true' : 'false' }},
        loginUrl: @js(route('login')),
    })"
>
    <a href="{{ $comment->user?->author_url ?? '#' }}" class="mt-1 shrink-0" @if (! $comment->user) aria-disabled="true" @endif>
        <x-avatar :user="$comment->user" size="sm" />
    </a>

    <div class="min-w-0 flex-1">
        <div class="rounded-2xl border border-line bg-surface p-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2 text-sm">
                    <a href="{{ $comment->user?->author_url ?? '#' }}" class="font-semibold text-content hover:text-primary">
                        {{ $comment->user?->name ?? 'Deleted user' }}
                    </a>
                    <span class="text-muted">·</span>
                    <time class="text-xs text-muted" datetime="{{ $comment->created_at?->toIso8601String() }}">
                        {{ $comment->created_at?->diffForHumans() }}
                    </time>
                </div>

                @if ($isOwn || $isManager)
                    <div class="flex items-center gap-1">
                        @if ($isOwn)
                            <button
                                type="button"
                                x-on:click="toggleEdit()"
                                class="rounded-lg p-1.5 text-muted transition-colors hover:bg-surface-alt hover:text-content"
                                aria-label="Edit comment"
                            >
                                <x-icon icon="pen" class="h-4 w-4" />
                            </button>
                        @endif
                        <button
                            type="button"
                            x-on:click="remove()"
                            x-bind:disabled="busy"
                            class="rounded-lg p-1.5 text-muted transition-colors hover:bg-danger-soft hover:text-danger"
                            aria-label="Delete comment"
                        >
                            <x-icon icon="trash" class="h-4 w-4" />
                        </button>
                    </div>
                @endif
            </div>

            <div x-show="! editing">
                <p class="mt-2 text-sm leading-relaxed text-content-soft text-pretty">{{ $comment->body }}</p>

                <div class="mt-3 flex items-center gap-4">
                    <button
                        type="button"
                        x-on:click="toggleLike()"
                        x-bind:disabled="liking"
                        class="inline-flex items-center gap-1.5 text-xs font-medium transition-colors"
                        x-bind:class="liked ? 'text-danger' : 'text-muted hover:text-danger'"
                        aria-label="Like comment"
                    >
                        <x-icon icon="heart" class="h-4 w-4" x-bind:class="liked ? 'fill-current' : ''" />
                        <span x-text="likeCount"></span>
                    </button>

                    @if ($comment->parent_id === null)
                        <button
                            type="button"
                            x-on:click="toggleReply()"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-muted transition-colors hover:text-primary"
                            aria-label="Reply to comment"
                        >
                            <x-icon icon="reply" class="h-4 w-4" />
                            Reply
                        </button>
                    @endif
                </div>
            </div>

            {{-- Edit form --}}
            <form
                x-show="editing"
                x-cloak
                x-on:submit.prevent="update()"
                class="mt-3"
                aria-label="Edit comment form"
            >
                <textarea
                    x-model="editBody"
                    rows="3"
                    class="input w-full"
                    required
                    placeholder="Edit your comment…"
                ></textarea>

                <p x-show="error" class="mt-2 text-sm text-danger" x-text="error"></p>

                <div class="mt-3 flex items-center gap-2">
                    <button type="submit" class="btn btn-primary btn-sm" x-bind:disabled="busy">
                        Save
                    </button>
                    <button type="button" x-on:click="toggleEdit()" class="btn btn-outline btn-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        {{-- Reply form --}}
        <form
            x-show="replying"
            x-cloak
            x-data="commentForm({
                postUrl: @js($postUrl),
                parentId: {{ $comment->id }},
            })"
            x-on:submit.prevent="submit()"
            class="mt-3"
            aria-label="Reply form"
        >
            <div class="flex gap-3">
                <textarea
                    x-model="body"
                    rows="2"
                    class="input w-full"
                    placeholder="Write a reply…"
                    required
                ></textarea>
                <button type="submit" class="btn btn-outline btn-sm self-start" x-bind:disabled="loading">
                    <span x-show="! loading">Reply</span>
                    <span x-show="loading">Posting…</span>
                </button>
            </div>
            <p x-show="error" class="mt-2 text-sm text-danger" x-text="error"></p>
            <p x-show="success" class="mt-2 text-sm text-success" x-text="success"></p>
        </form>

        {{-- Nested replies --}}
        @if ($comment->parent_id === null && $comment->replies->isNotEmpty())
            <div id="replies-{{ $comment->id }}" class="mt-4 space-y-4 border-l-2 border-line pl-4 sm:pl-6">
                @foreach ($comment->replies as $reply)
                    @include('comments.partials.comment', ['comment' => $reply])
                @endforeach
            </div>
        @endif
    </div>
</article>
