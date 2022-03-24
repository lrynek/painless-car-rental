ARG image
FROM $image AS dev

ARG user_name=composer
ARG uid=1000
ARG gid=1000

RUN addgroup -g $gid $username $gid || true && \
    adduser -D -h "/home/${user_name}" -u $uid -G $gid $user_name || true && \
    printf "PS1='\u@\h:[\W]\$ '" > "/home/${user_name}/.bashrc" && \
    mkdir -p "/app" "/home/$user_name/.config" "/home/${user_name}/.cache" && \
    chown -R $uid:$gid "/app" && \
    chown -R $uid:$gid "/home/${user_name}/.config" && \
    chown -R $uid:$gid "/home/${user_name}/.cache"

USER $uid

# https://docs.docker.com/engine/reference/builder/#cmd
# https://www.gnu.org/software/coreutils/manual/html_node/env-invocation.html#env-invocation

CMD /usr/bin/env bash
