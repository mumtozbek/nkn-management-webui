<?php

namespace Database\Factories;

use App\Models\Node;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NodeFactory extends Factory
{
    use HasFactory;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Node::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'host' => $this->faker->ipv4(),
        ];
    }
}
