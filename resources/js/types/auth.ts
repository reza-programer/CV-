export type User = {
    id: number;
    name: string;
    email: string;
    is_admin: boolean;
    /** The account photo, served only to its owner. */
    avatar_url: string | null;
};

export type Auth = {
    user: User;
};
