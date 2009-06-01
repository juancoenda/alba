<?php

require_once 'lib/model/om/BaseAlumno.php';


/**
 * Skeleton subclass for representing a row from the 'alumno' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package model
 */
class Alumno extends BaseAlumno {
    public function __toString() {
        return $this->getApellido() . " ".$this->getApellidoMaterno()." ". $this->getNombre() ;
    }

    public function getApellidos(){
        return $this->getApellido().' '.$this->getApellidoMaterno();
    }

    public function toArrayInforme($keyType = BasePeer::TYPE_PHPNAME)
    {
        return array(
            "Id"  => $this->getId(),
            "Nombre"  => $this->getNombre(),
            "ApellidoMaterno" => $this->getApellidoMaterno(),
            "Apellido" => $this->getApellido(),
            "FechaNacimiento" => $this->getFechaNacimiento(),
            "Direccion" => $this->getDireccion(),
            "Ciduad" => $this->getCiudad(),
            "CodigoPostal" => $this->getCodigoPostal(),
            'Provincia' => ($this->getProvincia())?$this->getProvincia()->getNombreCorto():'' ,
            "Telefono" => $this->getTelefono(),
            "LugarNacimiento" => $this->getLugarNacimiento(),
            'TipoDocumento' => ($this->getTipodocumento())?$this->getTipodocumento()->getNombre():'',
            "NroDocumento" => $this->getNroDocumento(),
            "Sexo" => $this->getSexo(),
            "Email" => $this->getEmail(),
            "DistanciaEscuela" => $this->getDistanciaEscuela(),
            "HermanosEscuela" => $this->getHermanosEscuela(),
            "HijoMaestroEscuela" => $this->getHijoMaestroEscuela(),
            'Establecimiento' => ($this->getEstablecimiento())?$this->getEstablecimiento()->getNombre():'',
            'Cuenta' => ($this->getCuenta())?$this->getCuenta()->getNombre():'',
            "CertificadoMedico" => $this->getCertificadoMedico(),
            "Activo" => $this->getActivo(),
            "FkConceptobajaId" => $this->getFkConceptobajaId(),
            'Pais' => ($this->getPais())?$this->getPais()->getNombreLargo():'',
        );

    }

    /**
     * Devuelve el legajo formateado
     **/
    public function getLegajo()
    {
      return trim($this->legajo_prefijo) .'-'. trim($this->legajo_numero);
    }

    public function getNotasConcepto() {
        $conceptoAlumno = array();
        $criteria = new Criteria();
        $criteria->add(BoletinConceptualPeer::FK_ALUMNO_ID, $this->getId());
        $aBoletinConceptual = BoletinConceptualPeer::doSelect($criteria);
        foreach($aBoletinConceptual as $boletinConceptual ) {
            if($boletinConceptual->getFkEscalanotaId()) {
                $conceptoAlumno[$boletinConceptual->getFkPeriodoId()][$boletinConceptual->getFkConceptoId()] = $boletinConceptual->getEscalanota()->getNombre();
            }
            if($boletinConceptual->getObservacion() != null) {
               $conceptoAlumno[$boletinConceptual->getFkPeriodoId()][$boletinConceptual->getFkConceptoId()] = $boletinConceptual->getObservacion();
            }
        }
        return $conceptoAlumno;
    }

    public function getNotas() {
        $notaAlumno = array();
        // notas del alumno
        $criteria = new Criteria();
        $criteria->add(BoletinActividadesPeer::FK_ALUMNO_ID, $this->getId());
        $criteria->addJoin(BoletinActividadesPeer::FK_ESCALANOTA_ID, EscalanotaPeer::ID);
        $criteria->addAsColumn("boletinActividades_periodo_id", BoletinActividadesPeer::FK_PERIODO_ID);
        $criteria->addAsColumn("boletinActividades_actividad_id", BoletinActividadesPeer::FK_ACTIVIDAD_ID);
        $criteria->addAsColumn("escalanota_nombre", EscalanotaPeer::NOMBRE);
        $aBoletinActividades = BasePeer::doSelect($criteria);
        foreach($aBoletinActividades as $boletinActividades) {
            $notaAlumno[$boletinActividades[0]][$boletinActividades[1]] = $boletinActividades[2];
        }
        return $notaAlumno;
    }


    public function getAsistenciasPorFechas($fecha_inicio, $fecha_fin) {
        $aAsistencia = array();

        // En teoria esta dos consultas pueden reemplazarse con una solo usando LEFT JOIN y CASE

        $c = new Criteria();
        $c->addSelectColumn(TipoasistenciaPeer::DESCRIPCION);
        $c->add(TipoasistenciaPeer::GRUPO, 'Inasistencias', Criteria::EQUAL);
        $rsColumna = TipoasistenciaPeer::doSelectStmt($c);

        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addGroupByColumn(TipoasistenciaPeer::DESCRIPCION);
//        $c->addSelectColumn(TipoasistenciaPeer::GRUPO);
        $c->addSelectColumn(TipoasistenciaPeer::DESCRIPCION);
        $c->addSelectColumn('SUM('.TipoasistenciaPeer::VALOR.') AS valor');
        $c->addJoin(TipoasistenciaPeer::ID, AsistenciaPeer::FK_TIPOASISTENCIA_ID);
        $c->add(AsistenciaPeer::FK_ALUMNO_ID, $this->getId(), Criteria::EQUAL);
        $c2 = new Criteria();
        $criterion = $c2->getNewCriterion(AsistenciaPeer::FECHA, $fecha_inicio, Criteria::GREATER_EQUAL);
        $criterion->addAnd($c2->getNewCriterion(AsistenciaPeer::FECHA, $fecha_fin, Criteria::LESS_EQUAL));
        $c->add($criterion);
        $c->add(TipoasistenciaPeer::GRUPO, 'Inasistencias', Criteria::EQUAL);
        $rsValor = TipoasistenciaPeer::doSelectStmt($c);

        if($rsColumna) {
            while($res_c = $rsColumna->fetch()) {
                 $aAsistencia[$res_c[0]] = 0;  // indice: nombre del Grupo, contenido: 
            }
        }
        if($rsValor) {
            while($res = $rsValor->fetch()) {
                // indice: nombre del Grupo, contenido: sumatoria de valor
                $aAsistencia[$res[0]] = $res[1];
            }
        }

        return $aAsistencia;
    }


    public function getAsistenciasPorCiclolectivo($ciclo_lectivo) {
        $aAsistencia = array(); 
        $criteria = new Criteria();
        $criteria->add(PeriodoPeer::FK_CICLOLECTIVO_ID, $ciclo_lectivo);
        $aPeriodo = PeriodoPeer::doSelect($criteria);
        foreach($aPeriodo as $periodo) {
            $aAsistencia[$periodo->getId()] = $this->getAsistenciasPorFechas($periodo->getFechaInicio(), $periodo->getFechaFin());
        }
        return $aAsistencia;
    }

} // Alumno
