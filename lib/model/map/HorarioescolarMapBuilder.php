<?php
		
require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'horarioescolar' table to 'alba' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an 
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive 
 * (i.e. if it's a text column type).
 *
 * @package model.map
 */	
class HorarioescolarMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'model.map.HorarioescolarMapBuilder';	

    /**
     * The database map.
     */
    private $dbMap;

	/**
     * Tells us if this DatabaseMapBuilder is built so that we
     * don't have to re-build it every time.
     *
     * @return boolean true if this DatabaseMapBuilder is built, false otherwise.
     */
    public function isBuilt()
    {
        return ($this->dbMap !== null);
    }

	/**
     * Gets the databasemap this map builder built.
     *
     * @return the databasemap
     */
    public function getDatabaseMap()
    {
        return $this->dbMap;
    }

    /**
     * The doBuild() method builds the DatabaseMap
     *
	 * @return void
     * @throws PropelException
     */
    public function doBuild()
    {
		$this->dbMap = Propel::getDatabaseMap('alba');
		
		$tMap = $this->dbMap->addTable('horarioescolar');
		$tMap->setPhpName('Horarioescolar');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'int', CreoleTypes::INTEGER, true, 11);

		$tMap->addColumn('DIA', 'Dia', 'int', CreoleTypes::INTEGER, true);

		$tMap->addColumn('HORA_INICIO', 'HoraInicio', 'int', CreoleTypes::TIME, true);

		$tMap->addColumn('HORA_FIN', 'HoraFin', 'int', CreoleTypes::TIME, true);

		$tMap->addColumn('NOMBRE', 'Nombre', 'string', CreoleTypes::VARCHAR, true);

		$tMap->addColumn('DESCRIPCION', 'Descripcion', 'string', CreoleTypes::VARCHAR, false);

		$tMap->addForeignKey('FK_ESTABLECIMIENTO_ID', 'FkEstablecimientoId', 'int', CreoleTypes::INTEGER, 'establecimiento', 'ID', true, null);

		$tMap->addForeignKey('FK_TURNOS_ID', 'FkTurnosId', 'int', CreoleTypes::INTEGER, 'turnos', 'ID', true, null);

		$tMap->addForeignKey('FK_HORARIOESCOLARTIPO_ID', 'FkHorarioescolartipoId', 'int', CreoleTypes::INTEGER, 'horarioescolartipo', 'ID', true, null);
				
    } // doBuild()

} // HorarioescolarMapBuilder