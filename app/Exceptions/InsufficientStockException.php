<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    /**
     * The products that could not be fulfilled.
     *
     * @var array<int, array<string, mixed>>
     */
    protected $shortages;

    /**
     * Create a new exception instance.
     *
     * @param  array<int, array<string, mixed>>  $shortages
     * @return void
     */
    public function __construct(array $shortages)
    {
        parent::__construct('Insufficient stock for one or more products.');

        $this->shortages = $shortages;
    }

    /**
     * Get the products that could not be fulfilled.
     *
     * @return array<int, array<string, mixed>>
     */
    public function shortages(): array
    {
        return $this->shortages;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * API clients get a 422 JSON body; browser forms are sent back with the
     * shortages as validation errors and their input preserved.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'errors' => [
                    'items' => $this->shortages,
                ],
            ], 422);
        }

        $messages = array_map(function (array $shortage) {
            return "Only {$shortage['available']} left of {$shortage['code']} (requested {$shortage['requested']}).";
        }, $this->shortages);

        return back()->withInput()->withErrors(['items' => $messages]);
    }
}
