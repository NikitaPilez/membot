<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <form class="mt-3" action="{{ url('download/video') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="url">URL (tiktok/instagram/youtube):</label>
            <input type="text" class="form-control" id="url" name="url">
        </div>
        <div class="form-group">
            <label for="comment">Комментарий</label>
            <textarea class="form-control" id="comment" name="comment" rows="4"></textarea>
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_prod" name="is_prod">
            <label class="form-check-label" for="promo">Прод?</label>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Отправить</button>
    </form>
    <form class="mt-5" action="{{ url('download/content') }}" method="POST">
        @csrf
        <div class="col-md-8">
            <label for="link">Ссылка на контент</label>
            <input type="text" class="form-control" id="url" name="url">
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_prod" name="is_prod">
            <label class="form-check-label" for="promo">Прод?</label>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Отправить</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>
