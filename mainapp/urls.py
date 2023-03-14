from django.urls import path
from . import views

urlpatterns = [
    path('', views.home_view, name="home"),
    path('about', views.about_view, name="about"),
    path('editorial', views.editorial_view, name="editorial"),
    path('for_author', views.for_author_view, name="for_author"),
    path('last_issue', views.last_issue_view, name="last_issue"),
    path('archive', views.archive_view, name="archive"),

    path('archive/2018', views.archive_2018, name='archive_2018'),
    path('archive/2019', views.archive_2019, name='archive_2019'),
    path('archive/2020', views.archive_2020, name='archive_2020'),
    path('archive/2021', views.archive_2021, name='archive_2021'),
    path('archive/2022', views.archive_2022, name='archive_2022'),
    path('archive/<int:year>', views.archive_year, name="archive"),

    path('contact', views.contact_view, name="contact"),
    path('article/<int:pk>', views.article_detail, name="detail"),
    path('download/<str:path>', views.download_page_view, name="download"),
    path('sitemap.xml/', views.open_xml_file, name='sitemap')
]
