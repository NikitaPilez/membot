<x-filament-panels::page>
    <form class="mt-3" action="{{ route('download.video') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="url">URL (tiktok/instagram/youtube):</label>
            <input type="text" class="form-control" id="url" name="url">
        </div>
        <div class="form-group">
            <label for="comment">Комментарий</label>
            <textarea class="form-control" id="comment" name="comment" rows="4"></textarea>
        </div>
        <div class="form-group">
            <label for="description">Описание к видео</label>
            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_prod" name="is_prod">
            <label class="form-check-label" for="promo">Прод?</label>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Отправить</button>
    </form>
    <form class="mt-5" action="{{ route('download.content') }}" method="POST">
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
</x-filament-panels::page>
