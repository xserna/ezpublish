CREATE TABLE ezdbfile (
    datatype    varchar(60)     DEFAULT 'application/octet-stream' NOT NULL,
    name        text            NOT NULL,
    name_hash   character(32)   DEFAULT '' NOT NULL PRIMARY KEY,
    scope       varchar(20)     DEFAULT '' NOT NULL,
    size        integer         DEFAULT 0 NOT NULL,
    mtime       integer         DEFAULT 0 NOT NULL,
    expired     integer         DEFAULT 0 NOT NULL,
    data        lo,

    UNIQUE ( name_hash )
);

CREATE INDEX ezdbfile_name  ON ezdbfile ( name );
CREATE INDEX ezdbfile_mtime ON ezdbfile ( mtime );