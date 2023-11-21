<h3>Просмотры за час: {{ $channelPost->stat?->views_after_hour ?? 0 }}, среднее кол-во просмотров за час: {{ $channelPost->getAverageCountViewsByTime('hour') }}</h3>
<h3>Просмотры за 6 часов:{{ $channelPost->stat?->views_after_sixth_hour ?? 0 }}, среднее кол-во просмотров за 6 часов: {{ $channelPost->getAverageCountViewsByTime('sixth') }}</h3>
<h3>Просмотры за 12 часов: {{ $channelPost->stat?->views_after_twelve_hour ?? 0 }}, среднее кол-во просмотров за 12 часов: {{ $channelPost->getAverageCountViewsByTime('twelve') }}</h3>
<h3>Просмотры за сутки: {{ $channelPost->stat?->views_after_day ?? 0}}, среднее кол-во просмотров за сутки: {{ $channelPost->getAverageCountViewsByTime('day') }}</h3>
