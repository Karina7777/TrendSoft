<?php

namespace Custom\EmployeeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Custom\EmployeeBundle\Entity\Employee;
use Custom\EmployeeBundle\Form\EmployeeType;

/**
 * Employee controller.
 *
 */
class EmployeeController extends Controller
{

    /**
     * Lists all Employee entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CustomEmployeeBundle:Employee')->findAll();

        return $this->render('CustomEmployeeBundle:Employee:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Employee entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Employee();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('employee_show', array('id' => $entity->getId())));
        }

        return $this->render('CustomEmployeeBundle:Employee:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Employee entity.
     *
     * @param Employee $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Employee $entity)
    {
        $form = $this->createForm(new EmployeeType(), $entity, array(
            'action' => $this->generateUrl('employee_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Employee entity.
     *
     */
    public function newAction()
    {
        $entity = new Employee();
        $form   = $this->createCreateForm($entity);

        return $this->render('CustomEmployeeBundle:Employee:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Employee entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CustomEmployeeBundle:Employee')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Employee entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CustomEmployeeBundle:Employee:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Employee entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CustomEmployeeBundle:Employee')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Employee entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CustomEmployeeBundle:Employee:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Employee entity.
    *
    * @param Employee $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Employee $entity)
    {
        $form = $this->createForm(new EmployeeType(), $entity, array(
            'action' => $this->generateUrl('employee_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Employee entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CustomEmployeeBundle:Employee')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Employee entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('employee_edit', array('id' => $id)));
        }

        return $this->render('CustomEmployeeBundle:Employee:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Employee entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CustomEmployeeBundle:Employee')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Employee entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('employee'));
    }

    /**
     * Creates a form to delete a Employee entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('employee_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Search a employee.
     *
     */
    public function searchAction(Request $request, $last_name) {
      $em = $this->getDoctrine()->getManager();
      $result = $em->getRepository('CustomEmployeeBundle:Employee')->findBy(array('lastName' => $last_name));
      $serializer = new Serializer(array(new ObjectNormalizer()), array(new JsonEncoder()) );
      $response = $serializer->serialize($result, 'json');

      return new Response($response, 200);
    }
}
