<?php
namespace Application\Bundle\DefaultBundle\Controller;

use Application\Bundle\DefaultBundle\Form\Type\DirectOrderFormType;
use Application\Bundle\DefaultBundle\Helpers\SeoOpenGraphEnum;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Application\Bundle\DefaultBundle\Form\Type\PromotionOrderFormType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Promotion controller. For promotions actions
 *
 * @Route("/promotion")
 */
class PromotionController extends Controller
{
    /**
     * Promotion index page by type
     *
     * @param Request $request Request
     * @param string  $type    Promotion type
     *
     * @return Response
     *
     * @Route("/{type}", requirements={"type" = "design|apps|development|sysadmin|mobilegames"}, name="page_promotion")
     */
    public function indexAction(Request $request, $type)
    {
        $form = $this->createForm(new PromotionOrderFormType());

        $seo = $this->get('sonata.seo.page');
        $seo->addMeta(
            'property',
            'og:url',
            $this->generateUrl(
                $request->get('_route'),
                [
                    'type' => $type,
                ],
                true
            )
        )
            ->addMeta('property', 'og:type', SeoOpenGraphEnum::ARTICLE);

        return $this->render('ApplicationDefaultBundle:Default:promotion_'.$type.'.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Order service request
     *
     * @param Request $request
     * @param string  $type
     *
     * @return Response
     *
     * @Route("/order/{type}", requirements={"type" = "design|apps|development|sysadmin|mobilegames"}, name="promotion_order")
     * @Method({"POST"})
     */
    public function orderAction(Request $request, $type)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException('Not supported');
        }

        $form = $this->createForm(new PromotionOrderFormType());
        $form->handleRequest($request);
        $translated = $this->get('translator');

        if ($form->isValid()) {
            $data = $form->getData();
            $email = $data['email'];
            $name  = $data['name'];

            $container = $this->get('service_container');
            $mailer_name = $container->getParameter('mailer_name');
            $mailer_notify = $container->getParameter('mailer_notify');
            $subject = $translated->trans('promotion.order.' . $type . '.mail.subject', ['%email%' => $email]);

            if ($this->get('application_default.service.mailer')->send(
                [$mailer_notify, $mailer_name],
                $subject,
                '@ApplicationDefault/emails/order_app.html.twig',
                [
                    'message' => $data['message'],
                    'name'    => $name,
                    'email'   => $email
                ]
            )) {
                return new JsonResponse(['status' => 'success']);
            } else {
                return new JsonResponse(['status' => 'error']);
            }
        } else {
            return new JsonResponse(['status' => 'error']);
        }
    }
}
