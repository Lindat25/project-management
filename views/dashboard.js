$(document).ready(function() {
    // Add Task
    $('#addTaskForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'add_task.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                let data = JSON.parse(response);
                if (data.success) {
                    alert('Task added successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            }
        });
    });

    // Edit Task
    window.editTask = function(taskId) {
        // Fetch task details and populate a form
        $.ajax({
            url: 'get_task.php',
            type: 'GET',
            data: { id: taskId },
            success: function(response) {
                let task = JSON.parse(response);
                $('#taskList li[data-task-id="' + taskId + '"]').html(`
                    <form class="editTaskForm">
                        <input type="hidden" name="id" value="${taskId}">
                        <input type="text" name="title" value="${task.title}" required>
                        <textarea name="description" required>${task.description}</textarea>
                        <input type="date" name="due_date" value="${task.due_date}" required>
                        <select name="status">
                            <option value="Not Started" ${task.status == 'Not Started' ? 'selected' : ''}>Not Started</option>
                            <option value="In Progress" ${task.status == 'In Progress' ? 'selected' : ''}>In Progress</option>
                            <option value="Completed" ${task.status == 'Completed' ? 'selected' : ''}>Completed</option>
                        </select>
                        <button type="submit">Save Changes</button>
                        <button type="button" onclick="cancelEdit(${taskId})">Cancel</button>
                    </form>
                `);
            }
        });
    }

    // Submit edited task
    $(document).on('submit', '.editTaskForm', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'edit_task.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                let data = JSON.parse(response);
                if (data.success) {
                    alert('Task updated successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            }
        });
    });

    // Delete Task
    window.deleteTask = function(taskId) {
        if (confirm('Are you sure you want to delete this task?')) {
            $.ajax({
                url: 'delete_task.php',
                type: 'POST',
                data: { id: taskId },
                success: function(response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        alert('Task deleted successfully');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                }
            });
        }
    }

    // Add Note
    $('#addNoteForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'add_note.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                let data = JSON.parse(response);
                if (data.success) {
                    alert('Note added successfully');
                    // Refresh notes
                    loadNotes($('#noteTaskId').val());
                } else {
                    alert('Error: ' + data.message);
                }
            }
        });
    });

    // Load Notes
    window.loadNotes = function(taskId) {
        $.ajax({
            url: 'get_notes.php',
            type: 'GET',
            data: { task_id: taskId },
            success: function(response) {
                let notes = JSON.parse(response);
                let notesHtml = '<ul>';
                notes.forEach(function(note) {
                    notesHtml += `<li>${note.content} (${note.created_at})</li>`;
                });
                notesHtml += '</ul>';
                $('#notesContent').html(notesHtml);
                $('#noteTaskId').val(taskId);
                $('#task-notes').show();
            }
        });
    }
});

function cancelEdit(taskId) {
    location.reload();
}