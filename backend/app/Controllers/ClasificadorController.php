<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\TipoImpuestoModel;
use App\Views\TipoImpuestoView;
use InvalidArgumentException;
use mysqli_sql_exception;

class ClasificadorController extends Controller
{
    private TipoImpuestoModel $impuestos;

    public function __construct()
    {
        parent::__construct();
        $this->impuestos = new TipoImpuestoModel();
    }

    /** Lista los clasificadores (tipo_impuesto). */
    public function index(): void
    {
        $items = $this->impuestos->all();

        $this->json([
            'success' => true,
            'data' => TipoImpuestoView::collection($items),
        ]);
    }

    /** Registra un nuevo clasificador. */
    public function store(): void
    {
        try {
            $nombre = Validator::requireString($this->input('nombre'), 'nombre');
            $descripcion = Validator::optionalString($this->input('descripcion'));
            $id = $this->impuestos->create($nombre, $descripcion);

            $this->json([
                'success' => true,
                'message' => 'Clasificador registrado correctamente.',
                'id' => $id,
            ], 201);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        } catch (mysqli_sql_exception $exception) {
            $this->error('No fue posible registrar el clasificador.', 500, ['error' => $exception->getMessage()]);
        }
    }
}
