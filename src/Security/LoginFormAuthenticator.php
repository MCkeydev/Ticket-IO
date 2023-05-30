/**
* Classe LoginFormAuthenticator
*
* Classe responsable de l'authentification du formulaire de connexion.
* Cette classe hérite de la classe AbstractLoginFormAuthenticator de Symfony.
*/
class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
use TargetPathTrait;

public const LOGIN_ROUTE = "app_login";

/**
* Constructeur de la classe LoginFormAuthenticator.
*
* @param UrlGeneratorInterface $urlGenerator Une instance de UrlGeneratorInterface pour générer les URLs.
*/
public function __construct(private UrlGeneratorInterface $urlGenerator)
{
}

/**
* Méthode authenticate
*
* Méthode chargée d'authentifier l'utilisateur en fonction des informations fournies dans la requête.
*
* @param Request $request La requête HTTP.
* @return Passport Le passeport d'authentification contenant les informations d'identification de l'utilisateur.
*/
public function authenticate(Request $request): Passport
{
$email = $request->request->get("email", "");

$request->getSession()->set(Security::LAST_USERNAME, $email);

return new Passport(
new UserBadge($email),
new PasswordCredentials($request->request->get("password", "")),
[
new RememberMeBadge(),
// new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
]
);
}

/**
* Méthode onAuthenticationSuccess
*
* Méthode appelée lorsque l'authentification réussit.
*
* @param Request $request La requête HTTP.
* @param TokenInterface $token Le token d'authentification.
* @param string $firewallName Le nom du pare-feu utilisé.
* @return Response|null La réponse HTTP de succès d'authentification.
*/
public function onAuthenticationSuccess(
Request $request,
TokenInterface $token,
string $firewallName
): ?Response {
if (
$targetPath = $this->getTargetPath(
$request->getSession(),
$firewallName
)
) {
return new RedirectResponse($targetPath);
}

// Par exemple :
return new RedirectResponse(
$this->urlGenerator->generate("app_accueil")
);
}

/**
* Méthode getLoginUrl
*
* Retourne l'URL de la page de connexion.
*
* @param Request $request La requête HTTP.
* @return string L'URL de la page de connexion.
*/
protected function getLoginUrl(Request $request): string
{
return $this->urlGenerator->generate(self::LOGIN_ROUTE);
}
}
