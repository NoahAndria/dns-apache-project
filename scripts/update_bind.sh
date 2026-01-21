#!/usr/bin/env bash
set -e

ACTION="$1"

case "$ACTION" in
  start|stop|reload|restart)
    systemctl "$ACTION" bind9
    ;;
  *)
    echo "Usage: $0 {start|stop|reload|restart}"
    exit 1
    ;;
esac

