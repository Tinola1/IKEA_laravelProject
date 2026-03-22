<x-admin-layout>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Product Reviews</h2>
                <p class="admin-page-subtitle">All customer reviews across the store.</p>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="admin-flash success" style="margin:var(--space-md) var(--space-lg) 0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-dashboard">

        {{-- Stat strip --}}
        <div class="admin-stat-grid" style="grid-template-columns:repeat(3,1fr);">
            <div class="admin-stat-card">
                <div class="admin-stat-icon orders">⭐</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Total Reviews</div>
                    <div class="admin-stat-value">{{ $totalReviews }}</div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon revenue">📊</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">Average Rating</div>
                    <div class="admin-stat-value">{{ $averageRating }}<span style="font-size:18px;color:var(--ikea-gray)">/5</span></div>
                </div>
            </div>
            <div class="admin-stat-card">
                <div class="admin-stat-icon products">🏆</div>
                <div class="admin-stat-body">
                    <div class="admin-stat-label">5-Star Reviews</div>
                    <div class="admin-stat-value">{{ $fiveStars }}</div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="admin-card" style="padding:var(--space-md);">
            <table id="reviewsTable" class="admin-table" style="width:100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Rating</th>
                        <th>Title</th>
                        <th>Review</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reviews as $review)
                        <tr>
                            <td class="order-id">#{{ $review->id }}</td>
                            <td>
                                <div class="table-customer-name">{{ $review->user->name }}</div>
                                <div class="table-customer-email">{{ $review->user->email }}</div>
                            </td>
                            <td>
                                <a href="{{ route('shop.show', $review->product) }}"
                                   class="table-action-link"
                                   target="_blank">
                                    {{ Str::limit($review->product->name, 30) }}
                                </a>
                            </td>
                            <td>
                                <div class="review-stars-admin">
                                    @for($i=1;$i<=5;$i++)
                                        <span class="{{ $i <= $review->rating ? 'star-admin-filled' : 'star-admin-empty' }}">★</span>
                                    @endfor
                                </div>
                                <div style="font-size:11px;color:var(--ikea-gray);font-weight:700;">{{ $review->rating }}/5</div>
                            </td>
                            <td style="font-weight:600;font-size:13px;">
                                {{ $review->title ?? '—' }}
                            </td>
                            <td style="font-size:13px;color:var(--ikea-gray);max-width:200px;">
                                {{ Str::limit($review->body ?? '—', 60) }}
                            </td>
                            <td class="order-date">{{ $review->created_at->format('M d, Y') }}</td>
                            <td>
                                <form method="POST"
                                      action="{{ route('admin.reviews.destroy', $review) }}"
                                      onsubmit="return confirm('Delete this review?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="table-action-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#reviewsTable').DataTable({
                    pageLength: 25,
                    order: [[6, 'desc']],
                    columnDefs: [{ orderable: false, targets: [7] }],
                    language: {
                        search: 'Search reviews:',
                        info: 'Showing _START_–_END_ of _TOTAL_ reviews',
                        paginate: { previous: '←', next: '→' },
                    },
                });
            });
        </script>
    @endpush

</x-admin-layout>