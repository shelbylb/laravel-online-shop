<?php
namespace App\DTOs;

use Spatie\LaravelData\Data;
use App\Http\Requests\Auth\RegisterRequest;

class RegisterDto extends Data
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public ?string $phone,
        public string $password,
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            $request->validated('first_name'),
            $request->validated('last_name'),
            $request->validated('email'),
            $request->validated('phone'),
            $request->validated('password'),
        );
    }
}
?>
