<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet"> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container mt-5">
    <h1>To Do List App</h1>
    <div class="row">
        <div class="col-6">
            <input type="text" id="taskName" class="form-control" placeholder="Task name"> 
        </div> 
        <div class="mb-5 col-6">
            <button id="saveTask" class="btn btn-primary mt-2">Add Task</button>
        </div>
    </div>  
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Task</th>
                <th>Status</th>
                <th>Action</th> 
            </tr>
        </thead>
        <tbody id="taskList">
        @foreach($tasks as $task)
            <tr data-id="{{ $task->id }}">
                <td>{{ $task->id }}</td>
                <td>{{ $task->name }}</td>
                <td>
                    <span class="status-text">{{ $task->status ? 'Done' : '' }}</span>
                </td>
                <td class="status-container"> 
                    <input type="checkbox" class="status-checkbox" {{ $task->status ? 'checked' : '' }}> 
                    <button class="btn btn-danger btn-sm delete-task">
                        <i class="bi bi-trash"></i>
                    </button>
                </td> 
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    // Add Task
    $('#saveTask').click(function() {
        const taskName = $('#taskName').val();
        
        $.post('{{ route('tasks.store') }}', {
            name: taskName,
            _token: '{{ csrf_token() }}'
        }).done(function(response) {  
            if (response.success && response.task) { 
                $('#taskName').val(''); 
                // Append the new task to the table
                $('#taskList').append(`
                    <tr data-id="${response.task.id}">
                        <td>${response.task.id}</td>
                        <td>${response.task.name}</td>
                        <td>
                            <span class="status-text"></span>
                        </td>
                        <td class="status-container"> 
                            <input type="checkbox" class="status-checkbox">
                            <button class="btn btn-danger btn-sm delete-task">
                            <i class="bi bi-trash"></i>
                            </button> 
                        </td>
                        
                    </tr>
                `);
            } else {
                alert(response.message);
            }
        }).fail(function(jqXHR) {
            if (jqXHR.status === 400) {
                alert(jqXHR.responseJSON.message);
            } else {
                alert('An error occurred while adding the task.');
            }
        });
    });
 
    // Update Task Status
    $('#taskList').on('change', '.status-checkbox', function() {
        const $checkbox = $(this);
        const taskId = $checkbox.closest('tr').data('id');
        const status = $checkbox.is(':checked');  
        const statusText = status ? 'Done' : '';

        $.ajax({
            url: '/tasks/' + taskId, 
            type: 'PATCH',
            data: {
                status: status ? 1 : 0,  
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) { 
                    $checkbox.closest('tr').find('.status-text').text(statusText);
                } else {
                    alert('Failed to update task status.');
                }
            },
            error: function(xhr) {
                alert('An error occurred while updating the task status.');
            }
        });
    });

    // Delete Task
    $('#taskList').on('click', '.delete-task', function() { 
        Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
        }).then((result) => {

        if (result.isConfirmed) {
            const taskId = $(this).closest('tr').data('id');
            $.ajax({
                url: '{{ url('/tasks') }}/' + taskId,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                }
            }).done(function() {
                Swal.fire({
                title: "Deleted!",
                text: "Your file has been deleted.",
                icon: "success"
                });
                $(this).closest('tr').remove();
            }.bind(this));
           
        }
        });
    });
});
</script>
</body>
</html>
