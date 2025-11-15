<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Student::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'student_id' => 'STU' . fake()->unique()->numerify('####'),
            'class' => fake()->randomElement(['9', '10', '11', '12']),
            'section' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'photo' => null,
        ];
    }

    /**
     * Indicate that the student is in a specific class.
     */
    public function inClass(string $class): static
    {
        return $this->state(fn (array $attributes) => [
            'class' => $class,
        ]);
    }

    /**
     * Indicate that the student is in a specific section.
     */
    public function inSection(string $section): static
    {
        return $this->state(fn (array $attributes) => [
            'section' => $section,
        ]);
    }
}
