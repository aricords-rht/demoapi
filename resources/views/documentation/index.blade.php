<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{$pageTitle}}</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
        <link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://getbootstrap.com/docs/4.0/examples/sticky-footer-navbar/sticky-footer-navbar.css" rel="stylesheet">

    </head>
    <body>
        <main role="main" class="container">
            <h1 class="mt-5">{{$pageTitle}}:</h1>
            <p class="lead">This is a JSON based REST api for handling Tasks. You can view all tasks, view a specific task, create new tasks, change existing tasks, execute tasks, and remove tasks. All requests require an API key which you can request from the owner of this website. The API key must be appended on to the endpoint like so: <code>/api/tasks?api_token=XXXXXXXX</code></p>
            <h2>Resource Action Examples:</h2>
            <p class="lead"><strong>View all:</strong> <code>$ curl http://ec2-3-17-155-132.us-east-2.compute.amazonaws.com/api/tasks?api_token=XXXXXXXX</code></p>
            <p class="lead"><strong>View single:</strong> <code>$ curl http://ec2-3-17-155-132.us-east-2.compute.amazonaws.com/api/tasks/1234?api_token=XXXXXXXX</code></p>
            <p class="lead"><strong>Create:</strong> <code>$ curl -X POST http://ec2-3-17-155-132.us-east-2.compute.amazonaws.com/api/tasks?api_token=XXXXXXXX -H "Content-Type: application/json" -d '{"type":"checklist","request_details":"TODO: Write a forking example using PHP proc_open()"}'</code></p>
            <p class="lead">When creating tasks, the fields <code>type</code> and <code>request_details</code> are saveable.</p>
            <p class="lead"><strong>Change:</strong> <code>$ curl -X PUT http://ec2-3-17-155-132.us-east-2.compute.amazonaws.com/api/tasks/1234?api_token=XXXXXXXX -H "Content-Type: application/json" -d '{"type":"checklist","request_details":"Nevermind, takes too much time"}'</code></p>
            <p class="lead">When changing a task, the field <code>request_details</code> is saveable.</p>
            <p class="lead"><strong>Execute:</strong> <code>$ curl -X PUT http://ec2-3-17-155-132.us-east-2.compute.amazonaws.com/api/tasks/1234?api_token=XXXXXXXX -H "Content-Type: application/json" -d '{"status":"ready"}'</code></p>
            <p class="lead">To execute a task, update the <code>status</code> to "ready" as shown above.</p>
            <p class="lead"><strong>Remove:</strong> <code>$ curl -X DELETE http://ec2-3-17-155-132.us-east-2.compute.amazonaws.com/api/tasks/1234?api_token=XXXXXXXX</code></p>
            <h2>Types of Tasks:</h2>
            <p>Below are a few different types of tasks you can create, as well as details of what they look like. Upon execution the results will be set to the "response_details" property of the structure representing the resource.</p>
            @foreach($taskTypes as $taskType)
                <h3>{{$taskType->name}}</h3>
                <p class="lead">{{$taskType->description}}</p>
                <p>Example request body:</p>
                <p class="lead"><code>{{$taskType->example_request}}</code></p>
            @endforeach
        </main>
    </body>
</html>
