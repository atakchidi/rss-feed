FROM node:8-alpine

ARG UID
ARG GID

RUN deluser --remove-home node \
  && addgroup -S node -g ${GID} \
  && adduser -S -G node -u ${UID} node \
  && apk add --update bash

USER node

WORKDIR /app

CMD ["node", "--version"]