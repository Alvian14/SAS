@extends('pages.index')

@section('admin_content')
<style>
    .notification-item {
        background: #fff;
        border-left: 4px solid #007bff;
        padding: 15px;
        margin-bottom: 12px;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .notification-item:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: translateX(5px);
    }

    .notification-item.unread {
        background: #f8f9ff;
        border-left-color: #0d6efd;
        font-weight: 500;
    }

    .notification-item.read {
        opacity: 0.8;
        border-left-color: #6c757d;
    }

    .notification-badge {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #0d6efd;
        margin-right: 8px;
    }

    .notification-item.read .notification-badge {
        background: #6c757d;
    }

    .notification-time {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .notification-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .notification-icon.info {
        background: #e7f1ff;
        color: #0d6efd;
    }

    .notification-icon.success {
        background: #d4edda;
        color: #198754;
    }

    .notification-icon.warning {
        background: #fff3cd;
        color: #ffc107;
    }

    .notification-icon.danger {
        background: #f8d7da;
        color: #dc3545;
    }

    .notification-actions {
        position: absolute;
        right: 15px;
        top: 15px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .notification-item:hover .notification-actions {
        opacity: 1;
    }

    .notification-item {
        position: relative;
    }

    .btn-notification-action {
        padding: 4px 8px;
        font-size: 0.85rem;
    }
</style>
<div class="container mt-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-bell"></i> Notifikasi
            </h1>
            <p class="text-muted small">Kelola semua pemberitahuan Anda di sini</p>
        </div>
        <div>
            <button class="btn btn-outline-secondary btn-sm" id="markAllRead">
                <i class="fas fa-check-double"></i> Tandai Semua Dibaca
            </button>
        </div>
    </div>

    <!-- Filter & Sort Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" id="searchNotification" placeholder="Cari notifikasi...">
            </div>
        </div>
        <div class="col-md-6">
            <select class="form-select" id="filterNotification">
                <option value="">Semua Notifikasi</option>
                <option value="unread">Belum Dibaca</option>
                <option value="read">Sudah Dibaca</option>
            </select>
        </div>
    </div>

    <!-- Notification List -->
    <div id="notificationContainer">
        <!-- Empty State -->
        <div id="emptyState" class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc;"></i>
            </div>
            <p class="text-muted">Tidak ada notifikasi</p>
        </div>

        <!-- Notification Items (akan di-generate dari backend) -->
        <div id="notificationList"></div>
    </div>
</div>

<!-- Modal untuk detail notifikasi -->
<div class="modal fade" id="notificationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger btn-sm" id="deleteNotificationBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>


@endsection
