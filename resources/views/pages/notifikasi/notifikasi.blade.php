@extends('pages.index')

@section('admin_content')
<style>
    /* Modern UI for Notifications */
    .notif-wrapper {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
        font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .notif-header-section {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: white;
        padding: 30px 40px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 10px 25px rgba(13, 110, 253, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notif-header-section h1 {
        font-weight: 700;
        font-size: 32px;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    .notif-header-section p {
        opacity: 0.9;
        font-size: 16px;
        margin: 0;
    }

    .btn-mark-all {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.4);
        padding: 10px 24px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s;
        backdrop-filter: blur(5px);
    }

    .btn-mark-all:hover {
        background: white;
        color: #0d6efd;
        border-color: white;
        transform: translateY(-2px);
    }

    .filters-container {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .search-box {
        flex: 1;
        position: relative;
    }

    .search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    .search-box input {
        width: 100%;
        padding: 12px 15px 12px 45px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #f9fafb;
        transition: all 0.3s;
    }

    .search-box input:focus {
        background: white;
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        outline: none;
    }

    .filter-select {
        min-width: 200px;
    }

    .filter-select select {
        width: 100%;
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #f9fafb;
        cursor: pointer;
    }

    .notif-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 16px;
        display: flex;
        gap: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid #f3f4f6;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .notif-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border-color: #e5e7eb;
    }

    .notif-card.unread::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 12px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.15);
        z-index: 2;
    }

    .notif-icon-wrap {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
        background: #eef2ff;
        color: #0d6efd;
    }

    .notif-actions {
        position: absolute;
        right: 20px;
        top: 20px;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .notif-card:hover .notif-actions {
        opacity: 1;
    }

    .btn-delete-notif {
        background: #fee2e2;
        color: #ef4444;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-delete-notif:hover {
        background: #ef4444;
        color: white;
        transform: scale(1.1);
    }

    .notif-content {
        flex: 1;
    }

    .notif-header-text {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .notif-title {
        font-weight: 700;
        font-size: 18px;
        color: #111827;
        margin: 0;
    }

    .notif-time {
        font-size: 13px;
        color: #6b7280;
        background: #f3f4f6;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 500;
    }

    .notif-message {
        color: #4b5563;
        font-size: 15px;
        line-height: 1.5;
        margin-bottom: 12px;
    }

    .notif-meta {
        display: flex;
        gap: 15px;
        font-size: 13px;
        color: #6b7280;
    }

    .notif-meta span {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #f9fafb;
        padding: 6px 12px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
    }

    .notif-meta i {
        color: #9ca3af;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .empty-state-icon {
        font-size: 64px;
        color: #e5e7eb;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: #374151;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .empty-state p {
        color: #6b7280;
    }
</style>
<div class="notif-wrapper mt-4">
    <!-- Header Section -->
    <div class="notif-header-section">
        <div>
            <h1><i class="fas fa-bell me-2"></i> Notifikasi</h1>
            <p>Kelola semua pemberitahuan Anda di sini</p>
        </div>
        <div>
            <button class="btn btn-mark-all" id="markAllRead">
                <i class="fas fa-check-double me-2"></i> Tandai Semua Dibaca
            </button>
        </div>
    </div>

    <!-- Filter & Sort Section -->
    <div class="filters-container">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchNotification" placeholder="Cari notifikasi...">
        </div>
        <div class="filter-select">
            <select id="filterNotification">
                <option value="">Semua Notifikasi</option>
                <option value="unread">Belum Dibaca</option>
                <option value="read">Sudah Dibaca</option>
            </select>
        </div>
    </div>

    <!-- Notification List -->
    <div id="notificationContainer">
        @if(isset($notifications) && $notifications->count() > 0)
            <div id="notificationList">
                @foreach($notifications as $notification)
                    <div class="notif-card unread" onclick="openNotificationModal(this, 'notifDetailModal-{{ $notification->id }}')"
                        data-id="{{ $notification->id }}"
                        data-title="{{ $notification->title }}"
                        data-message="{{ $notification->body }}"
                        data-time="{{ $notification->created_at?->format('d M Y H:i') }}"
                        data-sender="{{ $notification->sender->name ?? 'Sistem' }}"
                        data-class="{{ $notification->class->name ?? '-' }}">

                        <div class="notif-actions">
                            <form action="{{ route('notifikasi.destroy', $notification->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete-notif" title="Hapus Notifikasi" onclick="event.stopPropagation();">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>

                        <div class="notif-icon-wrap {{ $notification->type ?? '' }}">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="notif-content">
                            <div class="notif-header-text">
                                <h3 class="notif-title">{{ $notification->title }}</h3>
                                <span class="notif-time">{{ $notification->created_at?->format('d M Y H:i') }}</span>
                            </div>
                            <p class="notif-message">{{ Str::limit($notification->body, 100) }}</p>
                            <div class="notif-meta">
                                <span>
                                    <i class="fas fa-user"></i>
                                    {{ $notification->sender->name ?? 'Sistem' }}
                                </span>
                                @if($notification->class)
                                    <span>
                                        <i class="fas fa-chalkboard"></i>
                                        {{ $notification->class->name ?? '-' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="notifDetailModal-{{ $notification->id }}" tabindex="-1" aria-labelledby="notifDetailModalLabel-{{ $notification->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
                                <div class="modal-header" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: white; border: none; padding: 20px 24px;">
                                    <h5 class="modal-title" id="notifDetailModalLabel-{{ $notification->id }}" style="font-weight: 600;">
                                        <i class="fas fa-info-circle me-2"></i> Detail Notifikasi
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="padding: 24px;">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="notif-icon-wrap me-3" style="width: 40px; height: 40px; font-size: 16px;">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div>
                                            <h4 style="margin: 0; font-size: 18px; font-weight: 700; color: #111827;">{{ $notification->title }}</h4>
                                            <small style="color: #6b7280;">{{ $notification->created_at?->format('d M Y H:i') }}</small>
                                        </div>
                                    </div>
                                    <hr>
                                    <div style="background: #f9fafb; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                                        <p style="margin: 0; color: #4b5563; font-size: 15px; line-height: 1.6;">{{ $notification->body }}</p>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <div style="flex: 1; background: white; border: 1px solid #e5e7eb; padding: 12px; border-radius: 8px;">
                                            <span style="display: block; font-size: 12px; color: #6b7280; margin-bottom: 4px;">Pengirim</span>
                                            <div style="font-weight: 600; font-size: 14px; color: #111827;">
                                                <i class="fas fa-user text-primary me-1"></i> {{ $notification->sender->name ?? 'Sistem' }}
                                            </div>
                                        </div>
                                        <div style="flex: 1; background: white; border: 1px solid #e5e7eb; padding: 12px; border-radius: 8px;">
                                            <span style="display: block; font-size: 12px; color: #6b7280; margin-bottom: 4px;">Kelas</span>
                                            <div style="font-weight: 600; font-size: 14px; color: #111827;">
                                                <i class="fas fa-chalkboard text-primary me-1"></i> {{ $notification->class->name ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer" style="border-top: 1px solid #f3f4f6; padding: 16px 24px;">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 8px 20px;">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>Tidak ada notifikasi</h3>
                <p>Belum ada notifikasi baru untuk Anda saat ini.</p>
            </div>
        @endif
    </div>
</div>

<script>
    // kode untuk localStorage untuk menyimpan status baca notifikasi
    const READ_NOTIFICATION_STORAGE_KEY = 'sas_read_notifications';

    function getReadNotificationIds() {
        try {
            const savedValue = localStorage.getItem(READ_NOTIFICATION_STORAGE_KEY);
            const parsedValue = savedValue ? JSON.parse(savedValue) : [];
            return Array.isArray(parsedValue) ? parsedValue : [];
        } catch (error) {
            return [];
        }
    }

    function saveReadNotificationId(notificationId) {
        if (!notificationId) {
            return;
        }

        const readIds = new Set(getReadNotificationIds());
        readIds.add(String(notificationId));
        localStorage.setItem(READ_NOTIFICATION_STORAGE_KEY, JSON.stringify(Array.from(readIds)));
    }

    // Tandai semua notifikasi sebagai sudah dibaca
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchNotification');
        const filterSelect = document.getElementById('filterNotification');
        const notificationList = document.getElementById('notificationList');
        const notificationContainer = document.getElementById('notificationContainer');

        if (!searchInput || !notificationList) {
            return;
        }

        const cards = Array.from(notificationList.querySelectorAll('.notif-card'));
        const readIds = new Set(getReadNotificationIds());

        cards.forEach(function(card) {
            const cardId = card.getAttribute('data-id');
            if (cardId && readIds.has(String(cardId))) {
                card.classList.remove('unread');
            }
        });

        const searchEmptyState = document.createElement('div');
        searchEmptyState.className = 'empty-state';
        searchEmptyState.style.display = 'none';
        searchEmptyState.innerHTML = `
            <div class="empty-state-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>Notifikasi tidak ditemukan</h3>
            <p>Coba gunakan kata kunci lain.</p>
        `;
        notificationContainer.appendChild(searchEmptyState);

        function getCardSearchText(card) {
            const title = card.getAttribute('data-title') || '';
            const message = card.getAttribute('data-message') || '';
            const sender = card.getAttribute('data-sender') || '';
            const className = card.getAttribute('data-class') || '';
            const time = card.getAttribute('data-time') || '';

            return `${title} ${message} ${sender} ${className} ${time}`.toLowerCase();
        }

        function applyNotificationFilters() {
            const searchKeyword = searchInput.value.trim().toLowerCase();
            const statusFilter = filterSelect ? filterSelect.value : '';
            let visibleCount = 0;

            cards.forEach(function(card) {
                const matchesSearch = !searchKeyword || getCardSearchText(card).includes(searchKeyword);
                const isUnread = card.classList.contains('unread');
                const matchesStatus = !statusFilter
                    || (statusFilter === 'unread' && isUnread)
                    || (statusFilter === 'read' && !isUnread);

                const shouldShow = matchesSearch && matchesStatus;
                card.style.display = shouldShow ? '' : 'none';

                if (shouldShow) {
                    visibleCount += 1;
                }
            });

            searchEmptyState.style.display = visibleCount === 0 ? '' : 'none';
        }

        searchInput.addEventListener('input', applyNotificationFilters);

        if (filterSelect) {
            filterSelect.addEventListener('change', applyNotificationFilters);
        }

        window.applyNotificationFilters = applyNotificationFilters;
        applyNotificationFilters();
    });

    // Fungsi untuk menampilkan detail notifikasi di modal
    function openNotificationModal(card, modalId) {
        // Tandai sebagai sudah dibaca secara visual jika perlu
        card.classList.remove('unread');
        saveReadNotificationId(card.getAttribute('data-id'));

        if (typeof window.applyNotificationFilters === 'function') {
            window.applyNotificationFilters();
        }

        // Tampilkan modal
        var notifModal = new bootstrap.Modal(document.getElementById(modalId));
        notifModal.show();
    }
</script>

@endsection
