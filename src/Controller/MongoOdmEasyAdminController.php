<?php

namespace AlterPHP\EasyAdminExtensionBundle\Controller;

use AlterPHP\EasyAdminExtensionBundle\Security\AdminAuthorizationChecker;
use AlterPHP\EasyAdminMongoOdmBundle\Controller\EasyAdminController as BaseEasyAdminController;
use AlterPHP\EasyAdminMongoOdmBundle\Event\EasyAdminMongoOdmEvents;

class MongoOdmEasyAdminController extends BaseEasyAdminController
{
    public static function getSubscribedServices(): array
    {
        return \array_merge(parent::getSubscribedServices(), [AdminAuthorizationChecker::class]);
    }

    protected function embeddedListAction()
    {
        $this->dispatch(EasyAdminMongoOdmEvents::PRE_LIST);

        $fields = $this->document['list']['fields'];
        $paginator = $this->mongoOdmFindAll(
            $this->document['class'],
            $this->request->query->get('page', 1),
            $this->config['list']['max_results'] ?: 25,
            $this->request->query->get('sortField'),
            $this->request->query->get('sortDirection')
        );

        $this->dispatch(EasyAdminMongoOdmEvents::POST_LIST, ['paginator' => $paginator]);

        return $this->render('@EasyAdminExtension/default/embedded_list.html.twig', [
            'objectType' => 'document',
            'paginator' => $paginator,
            'fields' => $fields,
            'masterRequest' => $this->get('request_stack')->getMasterRequest(),
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException
     */
    protected function isActionAllowed($actionName)
    {
        switch ($actionName) {
            // autocomplete action is mapped to list action for access permissions
            case 'autocomplete':
            // embeddedList action is mapped to list action for access permissions
            case 'embeddedList':
                $actionName = 'list';
                break;
            default:
                break;
        }

        // Get item for edit/show or custom actions => security voters may apply
        $easyadminMongoOdm = $this->request->attributes->get('easyadmin_mongo_odm');
        $subject = $easyadminMongoOdm['item'] ?? null;
        $this->get(AdminAuthorizationChecker::class)->checksUserAccess($this->document, $actionName, $subject);

        return parent::isActionAllowed($actionName);
    }
}
