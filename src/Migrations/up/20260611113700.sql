-- =========================================
-- USER
-- =========================================
CREATE TABLE IF NOT EXISTS "user" (
    id UUID PRIMARY KEY,
    email TEXT NOT NULL,
    password TEXT NOT NULL,
    role TEXT NOT NULL,
    enabled BOOLEAN NOT NULL DEFAULT TRUE,
    deleted BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (email)
);

CREATE INDEX IF NOT EXISTS idx_user_email ON "user" (email, enabled);

-- =========================================
-- ACCESS GROUP
-- =========================================
CREATE TABLE IF NOT EXISTS "access_group" (
    id UUID PRIMARY KEY,
    name TEXT NOT NULL,
    enabled BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- =========================================
-- ACCESS GROUP PERMISSION
-- =========================================
CREATE TABLE IF NOT EXISTS "access_group_permission" (
    id UUID PRIMARY KEY,
    access_group_id UUID NOT NULL,
    context TEXT NOT NULL,
    permission TEXT NOT NULL,
    object_id UUID NULL DEFAULT NULL,
    FOREIGN KEY (access_group_id) REFERENCES access_group(id) ON DELETE CASCADE
);

-- =========================================
-- ACCESS GROUP USER
-- =========================================
CREATE TABLE IF NOT EXISTS "access_group_user" (
    access_group_id UUID NOT NULL,
    user_id UUID NOT NULL,
    PRIMARY KEY (access_group_id, user_id),
    FOREIGN KEY (access_group_id) REFERENCES access_group(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES "user"(id) ON DELETE CASCADE
);
