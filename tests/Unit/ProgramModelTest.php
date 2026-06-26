<?php

namespace Tests\Unit;

use App\Models\Program;
use PHPUnit\Framework\TestCase;

class ProgramModelTest extends TestCase
{
    public function test_program_returns_price_based_on_class_type(): void
    {
        $program = new Program([
            'price' => 750000,
            'private_price' => 1200000,
            'conversation_price' => 950000,
        ]);

        $this->assertSame(750000, $program->priceForClassType('Reguler'));
        $this->assertSame(1200000, $program->priceForClassType('Private'));
        $this->assertSame(950000, $program->priceForClassType('Conversation'));
    }

    public function test_program_formats_price_for_display(): void
    {
        $program = new Program([
            'price' => 850000,
        ]);

        $this->assertSame('Rp 850.000', $program->formattedPriceForClassType('Reguler'));
    }

    public function test_program_detects_class_type_variant_from_name(): void
    {
        $program = new Program([
            'name' => 'English for Kids - Private',
        ]);

        $this->assertSame([
            'base_name' => 'English for Kids',
            'class_type' => 'Private',
        ], $program->classTypeVariant());
    }
}
