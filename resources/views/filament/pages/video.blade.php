<x-filament-panels::page>
    <form class="mt-3" id="download" enctype="multipart/form-data" action="{{ route('download.video') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="url">URL (tiktok/instagram/youtube):</label>
            <input type="text" class="form-control" id="url" name="url">
        </div>
        <div class="form-group">
            <label for="content_url">URL (видео напрямую):</label>
            <input type="text" class="form-control" id="content_url" name="content_url">
        </div>
        <div class="form-group">
            <label for="comment">Комментарий</label>
            <textarea class="form-control" id="comment" name="comment" rows="4"></textarea>
        </div>
        <div class="form-group">
            <label for="description">Описание к видео</label>
            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
        </div>
        <div class="form-group">
            <label for="video">Видеофайл</label>
            <input type="file" class="form-control" id="video" name="video">
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_prod" name="is_prod">
            <label class="form-check-label" for="promo">Прод?</label>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Отправить</button>
    </form>
</x-filament-panels::page>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    function makeRequest() {
        $.ajax({
            url: 'http://127.0.0.1:8000/admin/download/status/',
            method: 'GET',
            success: function (response) {
                console.log(response.status);
            },
        });
    }

    setInterval(function () {
        makeRequest()
    }, 1000);
</script>
