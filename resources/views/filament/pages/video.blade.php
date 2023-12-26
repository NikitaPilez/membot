<style>
    .video-status {
        z-index: 25;
        display: block;
        position: fixed;
        top: 0;
        right: 0;
        color:  black;
        background-color: #e7e7e7;
        padding: 20px;
        margin: 10px;
        border-radius: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        animation: pulse 2s infinite;
    }

    #close-button {
        cursor: pointer;
        font-size: 18px;
        color: #333; /* Цвет крестика */
        position: absolute;
        top: 5px;
        right: 5px;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        50% {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }
        100% {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    }
</style>
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
    <div id="video-status">
        <div id="status"></div>
        <span id="close-button">&times;</span>
    </div>
</x-filament-panels::page>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    function makeRequest() {

        $.ajax({
            url: window.location.protocol + '//' + window.location.host + '/admin/download/status/get',
            method: 'GET',
            success: function (response) {
                console.log(response);

                if (response.status) {
                    $('#video-status').addClass('video-status')
                    $('#status').text('Статус загрузки видео - ' + response.status)
                } else {
                    $('#video-status').removeClass('video-status');
                }
            },
        });
    }

    $(function() {
        $('#close-button').click(function() {

            $('#video-status').removeClass('video-status');
            $('#status').text('');

            $.ajax({
                url: window.location.protocol + '//' + window.location.host + '/admin/download/status/delete',
                method: 'GET',
            });
        });
    });

    setInterval(function () {
        makeRequest()
    }, 1000);
</script>
