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
 * division Acciones
 *
 * @package    alba
 * @author     Jos� Luis Di Biase <josx@interorganic.com.ar>
 * @author     H�ctor Sanchez <hsanchez@pressenter.com.ar>
 * @author     Fernando Toledo <ftoledo@pressenter.com.ar>
 * @version    SVN: $Id$
 * @filesource
 * @license GPL
 */

class divisionActions extends autodivisionActions
{

     protected function addFiltersCriteria(&$c)
     {
         $c->addJoin(DivisionPeer::FK_TURNOS_ID, TurnosPeer::ID, Criteria::LEFT_JOIN);
         $c->addJoin(TurnosPeer::FK_CICLOLECTIVO_ID, CiclolectivoPeer::ID, Criteria::LEFT_JOIN);
         $c->add(CiclolectivoPeer::FK_ESTABLECIMIENTO_ID, $this->getUser()->getAttribute('fk_establecimiento_id'));
     }


}

?>