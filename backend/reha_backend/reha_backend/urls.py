from django.contrib import admin
from django.urls import path
from core import views
from projects.views import projects_list
from projects.views import project_detail

from news.views import news_list
from news.views import news_detail
from contact.views import contact_page

from django.conf import settings
from django.conf.urls.static import static


urlpatterns = [

    path('admin/', admin.site.urls),

    path('', views.home, name='home'),

    path('about/', views.about, name='about'),
    path('vision/', views.vision, name='vision'),
    path('values/', views.values, name='values'),

    path('education/', views.education, name='education'),
    path('health/', views.health, name='health'),
    path('emergency/', views.emergency, name='emergency'),
    path('women/', views.women, name='women'),
    path('livelihood/', views.livelihood, name='livelihood'),

    path('kabul/', views.kabul, name='kabul'),
    path('herat/', views.herat, name='herat'),
    path('wardak/', views.wardak, name='wardak'),
    path('takhar/', views.takhar, name='takhar'),
    path('panjshir/', views.panjshir, name='panjshir'),

    path('projects/', projects_list, name='projects'),
    path('projects/<int:id>/', project_detail, name='project_detail'),

    path('news/', news_list, name='news'),
    path('news/<int:id>/', news_detail, name='news_detail'),

    path('contact/', contact_page, name='contact'),
    path('donate/', views.donate, name='donate'),

]

urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
