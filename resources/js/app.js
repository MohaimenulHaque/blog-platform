import './bootstrap';

import Alpine from 'alpinejs';
import { Chart, registerables } from 'chart.js';

window.Alpine = Alpine;
Chart.register(...registerables);

window.__chart = (() => {
    const instances = new Map();

    const makeDataset = (config) => {
        const base = {
            borderWidth: 2,
            tension: 0.35,
            pointRadius: 3,
            pointHoverRadius: 5,
            fill: true,
        };

        return { ...base, ...config };
    };

    const defaults = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { labels: { usePointStyle: true, boxWidth: 8 } } },
    };

    const render = (id, type, labels, datasets, options = {}) => {
        const canvas = document.getElementById(id);

        if (! canvas) {
            return;
        }

        const existing = instances.get(id);

        if (existing) {
            existing.destroy();
            instances.delete(id);
        }

        const colors = {
            primary: '#6d28d9',
            secondary: '#d97706',
            success: '#16a34a',
            info: '#0284c7',
            danger: '#dc2626',
        };

        const chart = new Chart(canvas, {
            type,
            data: {
                labels,
                datasets: datasets.map((d) => ({
                    ...makeDataset(d),
                    backgroundColor:
                        d.backgroundColor ?? (Array.isArray(colors[d.color])
                            ? colors[d.color]
                            : (colors[d.color] + '33')),
                    borderColor: d.borderColor ?? colors[d.color],
                    pointBackgroundColor: d.pointBackgroundColor ?? colors[d.color],
                })),
            },
            options: {
                ...defaults,
                ...options,
            },
        });

        instances.set(id, chart);
    };

    return { render };
})();

class ApiError extends Error {
    constructor(data, status) {
        super(data.message || 'Something went wrong.');
        this.data = data;
        this.status = status;
    }
}

window.__api = (() => {
    const token = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

    const request = async (url, options = {}) => {
        const headers = {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(options.headers || {}),
        };

        const hasBody = options.body !== undefined;

        if (hasBody && !(options.body instanceof FormData)) {
            headers['Content-Type'] = 'application/json';
        }

        headers['X-CSRF-TOKEN'] = token();

        const response = await fetch(url, { ...options, headers });

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));

            throw new ApiError(data, response.status);
        }

        return response.status === 204 ? null : response.json();
    };

    return {
        request,
        get: (url) => request(url),
        post: (url, body) => request(url, { method: 'POST', body }),
        patch: (url, body) => request(url, { method: 'PATCH', body }),
        delete: (url) => request(url, { method: 'DELETE' }),
    };
})();

window.__hydrate = (el) => {
    if (window.Alpine) {
        window.Alpine.initTree(el);
    }
};

Alpine.data('newsletterForm', () => ({
    email: '',
    loading: false,
    success: '',
    error: '',

    async submit() {
        this.loading = true;
        this.success = '';
        this.error = '';

        try {
            const res = await window.__api.post('/newsletter/subscribe', { email: this.email });

            this.success = res.message;
            this.email = '';
        } catch (e) {
            this.error = e.data?.errors?.email?.[0] ?? e.message;
        } finally {
            this.loading = false;
        }
    },
}));

Alpine.data('toasts', (initial = []) => ({
    toasts: initial.map((t) => ({ ...t, show: false })),

    init() {
        this.toasts.forEach((toast, i) => {
            window.setTimeout(() => (toast.show = true), 80 + i * 120);
            window.setTimeout(() => (toast.show = false), 4200 + i * 120);
        });
    },
}));

Alpine.data('mediaPicker', (isPicker = 0) => ({
    isPicker: Boolean(isPicker),
    selectedUrl: '',
    selectedPath: '',
    selectedName: '',

    select(url, path, name) {
        if (! this.isPicker) {
            return;
        }

        this.selectedUrl = url;
        this.selectedPath = path;
        this.selectedName = name;
    },

    clearSelection() {
        this.selectedUrl = '';
        this.selectedPath = '';
        this.selectedName = '';
    },

    useSelected() {
        if (! this.selectedPath) {
            return;
        }

        window.parent.postMessage({
            type: 'media-selected',
            url: this.selectedUrl,
            path: this.selectedPath,
            name: this.selectedName,
        }, '*');
    },
}));

Alpine.data('dashboardCharts', (data = {}) => ({
    init() {
        const palette = ['primary', 'secondary', 'success', 'info', 'danger', 'secondary'];

        if (data.postsPublished) {
            window.__chart.render('chart-posts-published', 'line', data.labels, [{
                label: 'Posts published',
                data: data.postsPublished,
                color: 'primary',
            }]);
        }

        if (data.users) {
            const datasets = [{
                label: 'New users',
                data: data.users,
                color: 'success',
            }];

            if (data.comments) {
                datasets.push({
                    label: 'Comments',
                    data: data.comments,
                    color: 'info',
                });
            }

            window.__chart.render('chart-users', 'line', data.labels, datasets);
        }

        if (data.views) {
            window.__chart.render('chart-views', 'line', data.labels, [{
                label: 'Views',
                data: data.views,
                color: 'secondary',
            }]);
        }

        if (data.comments) {
            window.__chart.render('chart-comments', 'line', data.labels, [{
                label: 'Comments',
                data: data.comments,
                color: 'info',
            }]);
        }

        if (data.popularCategories) {
            window.__chart.render('chart-categories', 'bar', data.popularCategories.labels, [{
                label: 'Published posts',
                data: data.popularCategories.counts,
                color: 'primary',
                backgroundColor: palette.map((c) => c),
            }], {
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false } } },
            });
        }

        if (data.popularPosts) {
            window.__chart.render('chart-top-posts', 'bar', data.popularPosts.labels, [{
                label: 'Views',
                data: data.popularPosts.counts,
                color: 'success',
            }], {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true }, y: { grid: { display: false } } },
            });
        }
    },
}));

Alpine.data('postActions', (initial = {}) => ({
    liked: Boolean(initial.liked),
    likeCount: Number(initial.likeCount ?? 0),
    bookmarked: Boolean(initial.bookmarked),
    liking: false,
    bookmarking: false,
    copying: false,
    copied: false,

    async toggleLike() {
        if (! initial.auth) {
            window.location.href = initial.loginUrl;

            return;
        }

        this.liking = true;

        try {
            const res = await window.__api.post(`/posts/${initial.id}/like`);

            this.liked = res.liked;
            this.likeCount = Number(res.count ?? this.likeCount);
        } catch (e) {
            if (e.status === 419 || e.status === 401) {
                window.location.href = initial.loginUrl;
            }
        } finally {
            this.liking = false;
        }
    },

    async toggleBookmark() {
        if (! initial.auth) {
            window.location.href = initial.loginUrl;

            return;
        }

        this.bookmarking = true;

        try {
            const res = await window.__api.post(`/posts/${initial.id}/bookmark`);

            this.bookmarked = res.bookmarked;
        } catch (e) {
            if (e.status === 419 || e.status === 401) {
                window.location.href = initial.loginUrl;
            }
        } finally {
            this.bookmarking = false;
        }
    },

    async copyLink() {
        this.copying = true;

        try {
            await navigator.clipboard.writeText(initial.url);

            this.copied = true;

            window.setTimeout(() => (this.copied = false), 2000);
        } catch (e) {
            //
        } finally {
            this.copying = false;
        }
    },
}));

Alpine.data('commentForm', (options = {}) => ({
    body: '',
    parentId: options.parentId ?? null,
    loading: false,
    success: '',
    error: '',

    async submit() {
        if (this.body.trim().length < 2) {
            this.error = 'Your comment is too short.';

            return;
        }

        this.loading = true;
        this.success = '';
        this.error = '';

        try {
            const res = await window.__api.post(options.postUrl, {
                body: this.body,
                parent_id: this.parentId,
            });

            this.success = res.message;

            if (res.html) {
                const container = this.parentId
                    ? document.getElementById(`replies-${this.parentId}`)
                    : document.getElementById('comments-list');

                if (container) {
                    const template = document.createElement('template');
                    template.innerHTML = res.html.trim();
                    const node = template.content.firstElementChild;

                    if (node) {
                        container.appendChild(node);
                        window.__hydrate(node);
                    }
                }

                this.body = '';
            }
        } catch (e) {
            this.error = e.data?.errors?.body?.[0] ?? e.message;
        } finally {
            this.loading = false;
        }
    },
}));

Alpine.data('commentItem', (options = {}) => ({
    id: Number(options.id),
    body: options.body ?? '',
    liked: Boolean(options.liked),
    likeCount: Number(options.likeCount ?? 0),
    liking: false,
    replying: false,
    editing: false,
    editBody: options.body ?? '',
    busy: false,
    error: '',

    async toggleLike() {
        if (! options.auth) {
            window.location.href = options.loginUrl;

            return;
        }

        this.liking = true;

        try {
            const res = await window.__api.post(`/comments/${this.id}/like`);

            this.liked = res.liked;
            this.likeCount = Number(res.count ?? this.likeCount);
        } catch (e) {
            if (e.status === 419 || e.status === 401) {
                window.location.href = options.loginUrl;
            }
        } finally {
            this.liking = false;
        }
    },

    toggleReply() {
        this.replying = ! this.replying;
    },

    toggleEdit() {
        this.editing = ! this.editing;
        this.editBody = this.body;
        this.error = '';
    },

    async update() {
        this.busy = true;
        this.error = '';

        try {
            const res = await window.__api.patch(`/comments/${this.id}`, { body: this.editBody });

            this.body = this.editBody.trim();
            this.editing = false;
            this.error = '';
        } catch (e) {
            this.error = e.data?.errors?.body?.[0] ?? e.message;
        } finally {
            this.busy = false;
        }
    },

    async remove() {
        this.busy = true;

        try {
            await window.__api.delete(`/comments/${this.id}`);

            const el = document.getElementById(`comment-${this.id}`);

            el?.remove();
        } catch (e) {
            this.error = e.message;
        } finally {
            this.busy = false;
        }
    },
}));

Alpine.start();
