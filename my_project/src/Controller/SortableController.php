<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




class SortableController extends AbstractController
{
    private $field_name;

    ############################### get the entity class                             #############################
        public function getEntityClass($entity){
            $class_name = [];
            $class_name['class_name'] = 'App\Entity\\';
            $class_name['db_name']  = '';

            if($entity === 'client'){
                $class_name['class_name'] .= 'Client';
                $class_name['db_name']  = 'client';
            }
            else{
                $class_name['class_name'] = 'Add Entity Name in SortableController->getEntityClass';
            }
            return $class_name;
        }
    #
    ############################### move position by dragging                        #############################
        /**
         * @Route("/dragdrop/{id}/{entity}/{position}/{field_name}", name="dragdrop", options={"expose"=true})
         */
        public function dragdrop($entity ='client', $id = '2', $position = '4', $field_name)
        {

            # get the entity class
                $class_name = $this->getEntityClass($entity);
            # get field name
                $this->field_name = $field_name;
            # this is where the magic happend
            
                # old position is the position of the selected item
                    $oldPosition =$this->findPositionById($id, $class_name['class_name']);

                # set the new position's numbore from type string to integer
                    $newPosition = intval($position);
                
                # initiate variables 
                    $success = '';
                    $allPositionInRange= null;

                # if dragging is up to down, Ex => set item with pos == 1 to pos = 5
                    if($oldPosition < $newPosition){

                        # get all positions between old and new position  where oldPos < newPos
                            $allPositionInRange = $this->positionRange($oldPosition, $newPosition, $class_name['class_name']);
                    }
                # if dragging is down to up, Ex => set item with pos == 5 to pos = 1
                    elseif ($oldPosition > $newPosition) {

                        # get all positions between old and new position
                            $allPositionInRange = $this->positionRange($newPosition, $oldPosition, $class_name['class_name'], 'desc');
                    }
                
                # set the old position to 99999 to free space
                    $success = $this->resetPosition($oldPosition, 999999, $class_name['db_name']);

                # reset the postion of all items in the range of oldPos and newPos
                    for ($i = 0 ; $i < count($allPositionInRange) - 1; $i++){
                        $this->resetPosition($allPositionInRange[$i+1], $allPositionInRange[$i], $class_name['db_name']);
                    }

                # reset the val of old position to new position
                    $oldPosition= $this->findPositionById($id, $class_name['class_name']);
                    $success = $this->resetPosition($oldPosition, $newPosition, $class_name['db_name']);
            #

            return  new JsonResponse($allPositionInRange);
        }
    #
    ############################### move position by clicking                        #############################
        /**
         * @Route("/test/{newPosition}/{currentPosition}/{entity}/{direction}/{field_name}", name="top_bottom", options={"expose"=true})
         */
        public function topButtom($newPosition, $currentPosition, $direction, $entity, $field_name){

            $class_name = $this->getEntityClass($entity);
            $this->field_name = $field_name;
            if ($direction === 'bottom'){

                # get all positions between current and new position  where oldPos < newPos
                    $allPositionInRange = $this->positionRange($currentPosition, $newPosition, $class_name['class_name']);
            }
            elseif ('top'){
                
                # get all positions between old and new position
                    $allPositionInRange = $this->positionRange($newPosition, $currentPosition, $class_name['class_name'], 'desc');
            }
            
            # set the old position to 99999 to free space
                $success = $this->resetPosition($currentPosition, 999999, $class_name['db_name']);
                
            # reset the postion of all items in the range of oldPos and newPos
                for ($i = 0 ; $i < count($allPositionInRange) - 1; $i++){
                    $this->resetPosition($allPositionInRange[$i+1], $allPositionInRange[$i], $class_name['db_name']);
                }
            # reset the val of old position to new position
                $success = $this->resetPosition(999999, $newPosition, $class_name['db_name']);
        
            return  new JsonResponse( $allPositionInRange);
        }
    #
    ############################### move position by one step                        #############################
        /**
         * @Route("/movebyOne/{currentPosition}/{entity}/{direction}/{field_name}", name="movebyone", options={"expose"=true})
         */
        public function movebyone($currentPosition, $direction, $entity, $field_name){

            $class_name = $this->getEntityClass($entity);
            $this->field_name = $field_name;
            # get the new position
                $newPosition = $this->getPosByPos($currentPosition, $direction, $class_name['class_name']);

            # set the old position to 99999 to free space
                $success = $this->resetPosition($currentPosition, 999999, $class_name['db_name']);
            
            # set the newpos to oldpos val
                $success = $this->resetPosition($newPosition, $currentPosition, $class_name['db_name']);

            # reset the val of old position to new position
                $success = $this->resetPosition(999999, $newPosition, $class_name['db_name']);
                    
            return  new Response( $newPosition);
            
        }
    #
    ############################### set the first position= 0                        #############################
        /**
         * @Route("/checkposition/{currentPosition}/{entity}/{field_name}", name="checkposition", options={"expose"=true})
         */
        public function checkposition($currentPosition, $entity, $field_name){
            
            $class_name = $this->getEntityClass($entity);
            $this->field_name = $field_name;
            # get the first position
            $firstPosition = $this->getFirstPos($class_name['class_name']);
            
            if (intval($currentPosition) == $firstPosition){
                $success = $this->resetPosition($currentPosition, 0, $class_name['db_name']);
            }

            if (intval($currentPosition) === $firstPosition and $firstPosition === 0){
                return  new JsonResponse( ['stop']);
            }
            else {
                return  new JsonResponse( ['dont stop']);
            }     
        }
    #
    ############################### RepositoryMethod : Fined Position By Id          #############################
        public function findPositionById($id, $class_name)
        {
            $result = $this->getDoctrine()->getManager()->createQueryBuilder()
                ->select('elem.'.$this->field_name.'')
                ->from($class_name , 'elem')
                ->andWhere('elem.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getSingleResult();
            return $result[$this->field_name];
        }
    
    ############################### RepositoryMethod : get position Range            #############################
        // get all positions between oldPosition and newPosition where oldPos < newPos
        public function positionRange($oldPosition, $newPosition, $class_name, $order = 'asc')
        {
            $result = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('elem.'.$this->field_name)
            ->from($class_name , 'elem')
            ->andWhere('elem.'.$this->field_name.' >= :oldPos')
            ->andWhere('elem.'.$this->field_name.' <= :newPos')
            
            ->setParameter('oldPos', $oldPosition)
            ->setParameter('newPos', $newPosition)
            ->orderBy('elem.'.$this->field_name.'', $order)
            ->getQuery()->getResult();

            # clean result => set positions in array as int
            foreach ($result as $key => $value){
                $result[$key] = $value[$this->field_name];
            }
            return $result;
        }
    #
    ############################### RepositoryMethod : get position Range            #############################
        // reset position by old position
        public function resetPosition($oldPosition, $newPosition, $class_name)
        {
            // init entity manager 
            $em =$this->getDoctrine()->getManager();
            $connection = $em->getConnection();
            //excute query
            $excute = $connection->prepare("UPDATE ".$class_name." elem SET elem.".$this->field_name." = ".$newPosition." WHERE elem.".$this->field_name." = ".intval($oldPosition)." ;")->execute();
            // return response
            if($excute)
                return true;
            else
                return false;
        }
    #
    ############################### RepositoryMethod : get position by position      #############################
        // get the position above the curent position
        public function getPosByPos($currentPos, $direction, $class_name)
        {
        $em = $this->getDoctrine()->getManager();
        $result = $em->createQueryBuilder()->from($class_name , 'elem');
        if ($direction == 'up'){
            $result = $result->andWhere('elem.'.$this->field_name.' < :Pos')
                ->select('MAX(elem.'.$this->field_name.') AS '.$this->field_name)
                ->setParameter('Pos', $currentPos)
                ->getQuery()
                ->getSingleResult();
        }
        else{
            $result = $result->andWhere('elem.'.$this->field_name.' > :Pos')
                ->select('MIN(elem.'.$this->field_name.') AS '.$this->field_name)
                ->setParameter('Pos', $currentPos)
                ->getQuery()
                ->getSingleResult();
        }
        return $result[$this->field_name];
        }
    #
    ############################### RepositoryMethod : get the first position        #############################
        // get the position above the curent position
        public function getFirstPos($class_name)
        {
        $em = $this->getDoctrine()->getManager();
        $result = $em->createQueryBuilder()
            ->from($class_name , 'elem')
            ->select('MIN(elem.'.$this->field_name.') AS '.$this->field_name)
            ->getQuery()
            ->getSingleResult();
            return intval($result[$this->field_name]);
        }
    #
}