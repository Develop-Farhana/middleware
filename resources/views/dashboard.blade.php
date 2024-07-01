<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-image: url('https://example.com/background-image.jpg'); /* Replace with your background image URL */
            background-size: cover;
            background-position: center;
            color: #333; /* Default text color */
        }
        .card {
            margin-top: 20px;
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent white background for cards */
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Soft shadow */
        }
        .table-container {
            margin-top: 30px;
        }
        .badge {
            cursor: pointer;
        }
        .task-actions {
            white-space: nowrap;
        }
        .add-task-button {
            margin-bottom: 10px;
        }
        .modal-header {
            border-bottom: none; /* Remove border bottom from modal header */
        }
    </style>
    <!-- CSRF Token for AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Dashboard
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-danger">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
                <div class="card-body">
                    <h1>Hello, {{ Auth::user()->name }}</h1>
                    <button class="btn btn-primary add-task-button" data-toggle="modal" data-target="#addTaskModal">Add Task</button>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    Existing Tasks
                </div>
                <div class="card-body">
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
                                            <span class="badge {{ $task->status == 'done' ? 'badge-success' : 'badge-warning' }}" onclick="updateStatus({{ $task->id }}, '{{ $task->status == 'done' ? 'pending' : 'done' }}')">
                                                {{ $task->status }}
                                            </span>
                                        </td>
                                        <td class="task-actions">
                                            <button class="btn btn-sm btn-success mr-2" onclick="updateStatus({{ $task->id }}, 'done')"> Done</button>
                                            <button class="btn btn-sm btn-warning" onclick="updateStatus({{ $task->id }}, 'pending')"> Pending</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Add Task -->
<div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="add-task-form" action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addTaskModalLabel">Add Task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Task</button>
                </div>
            </form>
        </div>
    </div>
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
                    let statusText = status === 'done' ? 'Done' : 'Pending';
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

    $(document).ready(function() {
        $('#add-task-form').submit(function(event) {
            event.preventDefault(); // Prevent the form from submitting normally
            
            var formData = $(this).serialize(); // Serialize form data
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Clear form fields on success
                        $('#task').val('');
                        $('#status').val('pending');
                        
                        // Append new task to task list
                        var statusBadge = response.task.status === 'done' ? 'badge-success' : 'badge-warning';
                        var statusText = response.task.status === 'done' ? 'Done' : 'Pending';
                        var taskRow = `
                            <tr id="task-${response.task.id}">
                                <td>${response.task.task}</td>
                                <td>
                                    <span class="badge ${statusBadge}" onclick="updateStatus(${response.task.id}, '${response.task.status === 'done' ? 'pending' : 'done'}')">${statusText}</span>
                                </td>
                                <td class="task-actions">
                                    <button class="btn btn-sm btn-success mr-2" onclick="updateStatus(${response.task.id}, 'Done')"> Done</button>
                                    <button class="btn btn-sm btn-warning" onclick="updateStatus(${response.task.id}, 'Pending')"> Pending</button>
                                </td>
                            </tr>
                        `;
                        
                        $('#task-list').prepend(taskRow); // Insert as first row
                        
                        $('#addTaskModal').modal('hide'); // Hide the modal after adding task
                        
                        alert('Task added successfully!');
                    } else {
                        alert('Failed to add task. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Failed to add task. Please try again.');
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>

</body>
</html>
