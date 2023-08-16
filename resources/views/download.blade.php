<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>
<body>
<div class="mt-5 ms-2 me-2">
    <form class="row g-3" action="{{ asset('download/video') }}" method="POST">
        @csrf

        <div class="col-auto">
            <label for="text" class="visually-hidden">Label</label>
            <input type="text" readonly class="form-control-plaintext" id="text" value="Input inst/youtube/tiktok link">
        </div>
        <div class="col-md-8">
            <label for="link" class="visually-hidden">Input url</label>
            <input class="form-control" id="link" name="url" placeholder="Inst/youtube/tiktok link">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary mb-3">Send url link</button>
        </div>
    </form>
    <form class="row g-3 mt-5" action="{{ asset('download/content') }}" method="POST">
        @csrf

        <div class="col-auto">
            <label for="text" class="visually-hidden">Label</label>
            <input type="text" readonly class="form-control-plaintext" id="text" value="Content link">
        </div>
        <div class="col-md-8">
            <label for="link" class="visually-hidden">Input content</label>
            <input class="form-control" id="content" name="content_url" placeholder="Content link">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary mb-3">Send content link</button>
        </div>
    </form>
</div>
<div class="row">
    <a href="{{ route('send.telegram') }}" class="btn btn-primary">Send to telegram</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>
