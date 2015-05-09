<?php
use \ApiTester;

class HoyADondeCest
{
    public function _before(ApiTester $I)
    {
    }

    public function _after(ApiTester $I)
    {
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Pruebas a todas las peticiones GET de TODO
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    public function getDashboard(ApiTester $I)
    {
        $I->wantTo('obtener los datos para el dashboard');
        $I->sendGET('/v1/dashboard');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
        $I->seeResponseContains('categorias');
    }

    public function getCategorias(ApiTester $I)
    {
        $I->wantTo('Obtener todas las categorias');
        $I->sendGET('/v1/categorias');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
        $I->canSeeResponseContains('nombre');
    }

    public function getSubcategorias(ApiTester $I)
    {
        $I->wantTo('Obtener todas las subcategorias');
        $I->sendGET('/v1/subcategorias');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
        $I->canSeeResponseContains('categoria');
    }

    public function getLugares(ApiTester $I)
    {
        $I->wantTo('Obtener todos los lugares');
        $I->sendGET('/v1/lugares');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
        $I->canSeeResponseContains('coordenadas');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Pruebas a todas las peticiones GET con de un ID
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    public function getCategoria(ApiTester $I)
    {
        $I->wantTo('Obtener una categoria');
        $I->sendGET('/v1/categorias/1');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
        $I->canSeeResponseContains('nombre');
        $I->cantSee('"id":2');
    }

    public function getSubcategoria(ApiTester $I)
    {
        $I->wantTo('Obtener una subcategoria');
        $I->sendGET('/v1/subcategorias/1');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
        $I->canSeeResponseContains('categoria');
        $I->cantSee('"id":2');
    }

    public function getLugar(ApiTester $I)
    {
        $I->wantTo('Obtener un lugar');
        $I->sendGET('/v1/lugares/1');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
        $I->canSeeResponseContains('coordenadas');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Pruebas a todas las peticiones POST
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    public function createCategoria(ApiTester $I)
    {
        $I->wantTo('Crear una categoria');
        $params = array(
            "nombre" => "nombreTest",
            "icono" => "iconoTest",
            "descripcion" => "descripcionTest"
        );
        $I->sendPOST('/v1/categorias', $params);
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
        $I->canSeeResponseContainsJson(array('status'=>'Nueva categoria creada con éxito!'));
    }

    /**
     * @before getCategoria
     */
    public function createSubcategoria(ApiTester $I)
    {
        $I->wantTo('Crear una subcategoria');
        $params = array(
            "nombre" => "subcategoriaTest",
            "icono" => "iconoTest",
            "descripcion" => "descripionTest",
            "categoria" => 1
        );
        $I->sendPOST('/v1/subcategorias', $params);
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
        $I->canSeeResponseContainsJson(array('status' => 'Subcategoria creada con éxito!'));
    }


    /**
     * @before getCategoria
     * @before getSubcategoria
     */
    public function createLugar(ApiTester $I)
    {
        $I->wantTo('Crear un lugar');
        $params = array(
            'nombre' => "Lugar Test",
            'categoria' => 1,
            'subcategoria' => 1,
            'direccion' => "Calle Siempre Viva #123",
            'coordenadas' => "18.815723,-88.814993"
        );
        $I->sendPOST('/v1/lugares', $params);
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
        $I->canSeeResponseContainsJson(array('status' => 'Lugar creado con éxito'));
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Pruebas a todas las peticiones PUT
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    public function updateCategoria(ApiTester $I)
    {
        $I->wantTo('Actualizar una categoria');
        $params = array(
            'nombre' => "Categoria actualizada",
            'icono' => "Icono actualizado",
            'descripcion' => "Categoria actualizada"
        );
        $I->sendPUT('/v1/categorias/1', $params);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('status' => 'Categoria actualizada con éxito!'));
    }

    /**
     * @before getCategoria
     */
    public function updateSubcategoria(ApiTester $I)
    {
        $I->wantTo('Actualizar una subcategoria');
        $params = array(
            'nombre' => "Subcategoria actualizada",
            'icono' => "Icono subcategoria actualizada",
            'descripcion' => "Descripcion subcategoria actualizada",
            'categoria' => 7
        );
        $I->sendPUT('/v1/subcategorias/1', $params);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('status' => 'Subcategoria actualizada con éxito!'));
    }

    /**
     * @before getCategoria
     * @before getSubcategoria
     */
    public function updateLugar(ApiTester $I)
    {
        $I->wantTo('Actualizar un lugar');
        $params = array(
            'nombre' => "Lugar actualizado",
            'categoria' => 7,
            'subcategoria' => 7,
            'direccion' => "Direccio actualizada",
            'coordenadas' => "12.23423,-90.12312"
        );
        $I->sendPUT('/v1/lugares/1', $params);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('status' => 'Lugar actualizado con éxito'));
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Pruebas a todas las peticiones DELETE
    ////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @before getLugar
     */
    public function deleteLugar(ApiTester $I)
    {
        $I->wantTo("Eliminar un lugar");
        $I->sendDELETE('/v1/lugares/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('status' => 'Lugar eliminado con éxito.'));
    }

}