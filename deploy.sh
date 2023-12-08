#!/bin/bash
tmp=/dev/shm/membot
rm -rf ${tmp} && \
git clone -l -s . ${tmp} -b master && \
cd ${tmp} && \
echo "Building..." && \
composer i --no-dev --optimize-autoloader && \
rm -rf .env.example .gitattributes composer.lock .git* README.md && \
echo "Deploying..." && \
rsync -av --delete --exclude ".env" --exclude "/storage" ./ user@142.93.102.201:membot/ && \
ssh user@142.93.102.201 "cd membot && php artisan migrate --force && php artisan optimize && pm2 restart schedule queue-content queue-stat && php artisan storage:link" && \
echo "Done in ${SECONDS} sec."

