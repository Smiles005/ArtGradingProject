from .base import *
import ast

# SECURITY WARNING: don't run with debug turned on in production!
DEBUG = False

ALLOWED_HOSTS = ['checkmark.benedictine.edu']

# Database
# https://docs.djangoproject.com/en/4.1/ref/settings/#databases

DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.sqlite3',
        'NAME': BASE_DIR / '..' / '..' / 'production_db.sqlite3',
    }
}

AUTHENTICATION_BACKENDS = [
    'django_auth_adfs.backend.AdfsAuthCodeBackend'
]

# SECURITY WARNING: keep the secret key used in production secret!
with open(BASE_DIR / ".." / ".." / "svauwkrhwbioa_secretKey.txt") as f:
    SECRET_KEY = f.read().split("\n")[0]

# From Django: Using a secure-only CSRF cookie makes it more difficult for network traffic sniffers to steal the CSRF token.
CSRF_COOKIE_SECURE = True

SECURE_SSL_REDIRECT = True

INSTALLED_APPS.append('django_auth_adfs')

with open(BASE_DIR / ".." / ".." / "awrtgw4ehtrdnhhsrghej56_AuthADFSInformation.txt") as f:
    # Note: Make sure each of the following things is on one line of the ADFS info file 
    #           and is parsed correctly (e.g. if it should be parsed as a list, do so)
    authADFSInfo = f.read().strip().split("\n")

    AUTH_ADFS = { 
        "SERVER": authADFSInfo[0], 
        "CLIENT_ID": authADFSInfo[1], 
        "RELYING_PARTY_ID": authADFSInfo[2], 

        # Make sure to read the documentation about the AUDIENCE setting 
        # when you configured the identifier as a URL! 
        "AUDIENCE": authADFSInfo[3],  
        "CLAIM_MAPPING": ast.literal_eval(authADFSInfo[4]),
        "USERNAME_CLAIM": authADFSInfo[5],
    } 

# Configure django to redirect users to the right URL for login
LOGIN_URL = "django_auth_adfs:login"
LOGIN_REDIRECT_URL = "/greet"

# With this you can force a user to login without using
    # the LoginRequiredMixin on every view class
    #
    # You can specify URLs for which login is not enforced by
    # specifying them in the LOGIN_EXEMPT_URLS setting.
MIDDLEWARE.append('django_auth_adfs.middleware.LoginRequiredMiddleware')