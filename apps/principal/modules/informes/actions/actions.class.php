<?php
/**
 *    This file is part of Alba.
 *
 *    Alba is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation; either version 2 of the License, or
 *    (at your option) any later version.
 *
 *    Alba is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with Alba; if not, write to the Free Software
 *    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


/**
 * informes actions
 *
 * @package    alba
 * @author     José Luis Di Biase <josx@interorganic.com.ar>
 * @author     Héctor Sanchez <hsanchez@pressenter.com.ar>
 * @author     Fernando Toledo <ftoledo@pressenter.com.ar>
 * @version    SVN: $Id: actions.class.php 4099 2007-01-29 18:59:24Z josx $
 * @filesource
 * @license GPL
 */

class InformesActions extends sfActions
{

    public function preExecute() {
        $this->vista = $this->getRequestParameter('vista');
    }


/**
 *  Informe: Alumnos por Division 
 *
 */

     public function handleErrorAlumnosPorDivisionListado() {
        $this->forward('informes','alumnosPorDivisionFormulario');
     }

    public function executeAlumnosPorDivisionFormulario() {
        // inicializando variables
        $optionsDivision = array();
        
        // tomando los datos del formulario
        $division_id = $this->getRequestParameter('division_id');

        // llenando el combo de division segun establecimiento
        $establecimiento_id = $this->getUser()->getAttribute('fk_establecimiento_id');
        $criteria = new Criteria();
        $criteria->add(AnioPeer::FK_ESTABLECIMIENTO_ID, $establecimiento_id);
        $divisiones = DivisionPeer::doSelectJoinAnio($criteria);
        $optionsDivision['']  = "";
        foreach($divisiones as $division) {
            $optionsDivision[$division->getId()] = $division->getAnio()->getDescripcion()." ".$division->getDescripcion();
        }
        asort($optionsDivision);
        
        // asignando variables para ser usadas en el template
        $this->optionsDivision = $optionsDivision;
        $this->division_id = $division_id;
    }
    
    public function executeAlumnosPorDivisionListado() {

        // inicializando variables
        $aAlumno  = array();        

        // tomando los datos del formulario
        $division_id = $this->getRequestParameter('division_id');

        // buscando division
        $division = DivisionPeer::retrieveByPK($division_id);
        
        // buscando alumnos
        $criteria = new Criteria();
        $criteria->add(DivisionPeer::ID, $division_id);
        $criteria->addJoin(RelAlumnoDivisionPeer::FK_ALUMNO_ID, AlumnoPeer::ID);
        $criteria->addJoin(RelAlumnoDivisionPeer::FK_DIVISION_ID, DivisionPeer::ID);
        $criteria->addAscendingOrderByColumn(AlumnoPeer::APELLIDO);

        $alumnos = AlumnoPeer::doSelect($criteria);

        // asignando variables para ser usadas en el template        
        $this->aAlumno = $alumnos;
        $this->division = $division;

        $this->vista = "imprimir";

    }    


    public function executeConstanciaAlumnoRegularFormulario() {
        
        // inicializando variables
        $optionsDivision = array();
        $aAlumno  = array();        

        // tomando los datos del formulario
        $division_id = $this->getRequestParameter('division_id');
        $txt = $this->getRequestParameter('txt');

        // llenando el combo de division segun establecimiento
        $establecimiento_id = $this->getUser()->getAttribute('fk_establecimiento_id');
        $criteria = new Criteria();
        $criteria->add(AnioPeer::FK_ESTABLECIMIENTO_ID, $establecimiento_id);
        $divisiones = DivisionPeer::doSelectJoinAnio($criteria);
        $optionsDivision[]  = "";
        foreach($divisiones as $division) {
            $optionsDivision[$division->getId()] = $division->getAnio()->getDescripcion()." ".$division->getDescripcion();
        }
        asort($optionsDivision);
       
        if ($this->getRequest()->getMethod() == sfRequest::POST) {
            // buscando alumnos
            $criteria = new Criteria();
            if($division_id) {
                $criteria->add(DivisionPeer::ID, $division_id);
            }
            $criteria->addJoin(RelAlumnoDivisionPeer::FK_ALUMNO_ID, AlumnoPeer::ID);
            $criteria->addJoin(RelAlumnoDivisionPeer::FK_DIVISION_ID, DivisionPeer::ID);
            $criteria->addJoin(DivisionPeer::FK_ANIO_ID, AnioPeer::ID);
        
            if($txt) {
                $cton1 = $criteria->getNewCriterion(AlumnoPeer::NOMBRE, "%$txt%", Criteria::LIKE);
                $cton2 = $criteria->getNewCriterion(AlumnoPeer::APELLIDO, "%$txt%", Criteria::LIKE);
                $cton1->addOr($cton2);
                $criteria->add($cton1);
            }

            $criteria->addAsColumn("alumno_id", AlumnoPeer::ID);
            $criteria->addAsColumn("alumno_nombre", AlumnoPeer::NOMBRE);
            $criteria->addAsColumn("alumno_apellido", AlumnoPeer::APELLIDO);
            $criteria->addAsColumn("division_id", DivisionPeer::ID);
            $criteria->addAsColumn("division_descripcion", DivisionPeer::DESCRIPCION);
            $criteria->addAsColumn("anio_descripcion", AnioPeer::DESCRIPCION);

            $alumnos = BasePeer::doSelect($criteria);
            foreach($alumnos as $alumno) {
                $aAlumno[] = (object) array( 'alumno_id' => $alumno[0],'alumno_nombre' => $alumno[1], 'alumno_apellido' => $alumno[2], 'division_id' => $alumno[3], 'division_nombre' => $alumno[4], 'anio_descripcion' => $alumno[5] );
            }
    
        }

        // asignando variables para ser usadas en el template
        $this->optionsDivision = $optionsDivision;
        $this->division_id = $division_id;
        $this->txt = $txt;
        $this->aAlumno = $aAlumno;


    }

    public function executeConstanciaAlumnoRegularListado() {

        // tomando los datos del formulario
        $alumno_id = $this->getRequestParameter('alumno_id');
        $division_id = $this->getRequestParameter('division_id');
        $establecimiento_id = $this->getUser()->getAttribute('fk_establecimiento_id');
        $ciclolectivo_id = $this->getUser()->getAttribute('fk_ciclolectivo_id');

        $alumno = AlumnoPeer::retrieveByPK($alumno_id);
        $division = DivisionPeer::retrieveByPK($division_id);
        $establecimiento = EstablecimientoPeer::retrieveByPK($establecimiento_id);
        $turnos = TurnosPeer::retrieveByPK($division->getFkTurnosId());

        $criteria = new Criteria();
        $criteria->add(RelEstablecimientoLocacionPeer::FK_ESTABLECIMIENTO_ID, $establecimiento_id);
        $criteria->addJoin(RelEstablecimientoLocacionPeer::FK_LOCACION_ID, LocacionPeer::ID);
        $criteria->add(LocacionPeer::PRINCIPAL, true);
        $locacion = LocacionPeer::doSelectOne($criteria);

        // asignando variables para ser usadas en el template
        $this->alumno = $alumno;
        $this->division = $division;
        $this->establecimiento = $establecimiento;
        $this->turnos = $turnos;
        $this->locacion = $locacion;
        $this->vista = "imprimir";
    }




}
?>