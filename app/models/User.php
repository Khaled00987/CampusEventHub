<?php
/**
 * User.php — User account data access.
 *
 * Handles registration, login lookup, and email uniqueness checks.
 * Passwords are hashed with password_hash() before storage.
 */

declare(strict_types=1);

class User extends Model
{
    /**
     * Find user by email for login (includes password hash for verification).
     *
     * @param string $email
     * @return array<string, mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT * FROM users WHERE email = :email LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * @param int $id
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, name, email, role, status, created_at, updated_at FROM users WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Check if email is already registered.
     *
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE email = :email';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Create a new standard user (registration never creates admin).
     *
     * @param string $name
     * @param string $email
     * @param string $plainPassword
     * @return int New user ID
     */
    public function create(string $name, string $email, string $plainPassword): int
    {
        $hash = password_hash($plainPassword, PASSWORD_BCRYPT);
        $sql = 'INSERT INTO users (name, email, password, role, status, created_at, updated_at)
                VALUES (:name, :email, :password, :role, :status, NOW(), NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hash,
            'role' => 'user',
            'status' => 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }
}
