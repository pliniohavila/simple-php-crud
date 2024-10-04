CREATE TABLE IF NOT EXISTS public.tasks (
    id SERIAL PRIMARY KEY, 
    title varchar(32) NOT NULL,
    description varchar(128) NOT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP,
    completed_at TIMESTAMP
);