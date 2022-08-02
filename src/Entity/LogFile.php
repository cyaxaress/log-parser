<?php

namespace App\Entity;

use App\Repository\LogFileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogFileRepository::class)]
class LogFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $checked_at = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $cursor_line = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getCheckedAt(): ?\DateTimeInterface
    {
        return $this->checked_at;
    }

    public function setCheckedAt(?\DateTimeInterface $checked_at): self
    {
        $this->checked_at = $checked_at;

        return $this;
    }

    public function getCursorLine(): ?string
    {
        return $this->cursor_line;
    }

    public function setCursorLine(string $cursor_line): self
    {
        $this->cursor_line = $cursor_line;

        return $this;
    }
}
