<!-- resources/views/dashboard.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin-top: 20px;
        }
        .table-container {
            margin-top: 30px;
        }
    </style>
    <!-- CSRF Token for AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>
                <div class="card-body">
                    <h1>Hello, {{ Auth::user()->name }}</h1>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-danger">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container table-container">
<div class="card mt-4">
    <div class="card-header">Add Task</div>
    <div class="card-body">
        <form id="add-task-form">
            <div class="form-group">
                <label for="task">Task</label>
                <input type="text" class="form-control" id="task" name="task" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="pending">Pending</option>
                    <option value="done">Done</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Task</button>
        </form>
    </div>
</div>

    <h1 class="text-center mt-4">Existing Tasks</h1>
    @if($tasks->isEmpty())
        <div class="alert alert-info text-center">No tasks found.</div>
    @else
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="task-list">
                @foreach($tasks as $task)
                    <tr id="task-{{ $task->id }}">
                        <td>{{ $task->task }}</td>
                        <td>
                            <span class="badge {{ $task->status == 'done' ? 'badge-success' : 'badge-warning' }}">
                                {{ $task->status }}
                            </span>
                        </td>
                    
                        <td>
                        <button class="btn btn-sm btn-success" onclick="updateStatus({{ $task->id }}, 'done')">Mark as Done</button>
                        <button class="btn btn-sm btn-warning" onclick="updateStatus({{ $task->id }}, 'pending')">Mark as Pending</button>
                    </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- AJAX for updating task status and adding new tasks -->
<script>
    function updateStatus(taskId, status) {
        $.ajax({
            url: '{{ route('tasks.updateStatus') }}',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                task_id: taskId,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    let statusBadge = status === 'done' ? 'badge-success' : 'badge-warning';
                    let statusText = status === 'done' ? 'done' : 'Pending';
                    $('#task-' + taskId + ' .badge')
                        .removeClass('badge-success badge-warning')
                        .addClass(statusBadge)
                        .text(statusText);
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Failed to update task status. Please try again.');
                console.error(xhr.responseText);
            }
        });
    }
</script>

</body>
</html>
