<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Submitted</th>
                <th>User</th>
                <th>Type</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($feedback as $f)
                <tr>
                    <td>{{ $f->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $f->user?->full_name ?? 'Guest' }}</td>
                    <td>{{ ucfirst($f->type) }}</td>
                    <td>
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $f->rating)
                                <i class="fas fa-star text-warning"></i>
                            @else
                                <i class="far fa-star text-muted"></i>
                            @endif
                        @endfor
                    </td>
                    <td>{{ \Illuminate\Support\Str::limit($f->comment, 80) }}</td>
                    <td>
                        <span class="badge bg-{{ $f->status === 'pending' ? 'warning' : ($f->status === 'resolved' ? 'success' : 'info') }}">
                            {{ ucfirst($f->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.feedback.show', $f->feedback_id) }}" class="btn btn-sm btn-outline-primary">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-3" id="feedback-pagination">
    {{ $feedback->links() }}
</div>
