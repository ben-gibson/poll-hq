#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
  CREATE DATABASE poll_hq_read;
  GRANT ALL PRIVILEGES ON DATABASE read TO ${POSTGRES_USER};
EOSQL