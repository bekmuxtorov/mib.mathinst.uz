from django.contrib import messages
import xml.dom.minidom
from django.http import HttpResponse
import datetime
import os
from django.http import Http404, HttpResponse
from django.shortcuts import render

from config import settings
from . import models
from . import forms

# Create your views here.


def home_view(request):
    year = datetime.datetime.now().year
    years = list(range(2023, year+1))
    context = {"years": years}
    return render(request, 'index.html', context)


def about_view(request):
    year = datetime.datetime.now().year
    years = list(range(2023, year+1))
    context = {"years": years}
    return render(request, 'jurnal-haqida.html')


def editorial_view(request):
    year = datetime.datetime.now().year
    years = list(range(2023, year+1))
    context = {"years": years}
    return render(request, 'tahririyat.html', context)


def for_author_view(request):
    form = forms.Article()
    if request.method == "POST":
        print(request.POST)
        form = forms.Article(request.POST)
        if form.is_valid():
            author_uz = request.POST.get("author_uz")
            name_uz = request.POST.get("name_uz")
            name_en = request.POST.get("name_en")
            keywords_uz = request.POST.get("keywords_uz")
            keywords_en = request.POST.get("keywords_en")
            short_data_uz = request.POST.get("short_data_uz")
            short_data_en = request.POST.get("short_data_en")
            references = request.POST.get("references")
            file_link = request.POST.get("file_link")
            models.Article(
                author_uz=author_uz,
                author_en=author_uz,
                name_uz=name_uz,
                name_en=name_en,
                keywords_uz=keywords_uz,
                keywords_en=keywords_en,
                short_data_uz=short_data_uz,
                short_data_en=short_data_en,
                references=references,
                file_link=file_link
            ).save()
            form = forms.Article()
            messages.info(request, 'Muaffiqqiyatli jo\'natildi!')
        else:
            messages.info(request, 'Xatolik! Qayta urinib ko\'ring')

    year = datetime.datetime.now().year
    years = list(range(2023, year+1))
    context = {"years": years, "form": form}
    return render(request, 'mualliflar-uchun.html', context)


def last_issue_view(request):
    year = datetime.datetime.now().year
    years = list(range(2023, year+1))
    last_issue = models.Issue.objects.order_by("-created_at").first()
    articles = models.Article.objects.filter(
        issue=last_issue
    ).filter(
        status='open'
    ).order_by("ordinal_number")

    context = {
        "last_issue": last_issue,
        "articles": articles,
        "years": years
    }
    return render(request, 'oxirgi-son.html', context)


def article_detail(request, pk):
    choose_article = models.Article.objects.get(pk=pk)
    authors = choose_article.author_en.split(';')
    references = choose_article.references.split(';')
    article_value = choose_article.last_page - choose_article.first_page
    article_date = choose_article.created_at.strftime("%Y/%m/%d")
    year = datetime.datetime.now().year
    years = list(range(2023, year+1))
    vol = year - 2018 + 1
    context = {
        "article": choose_article,
        "authors": authors,
        "references": references,
        "article_value": article_value,
        "article_date": article_date,
        "years": years
    }
    return render(request, 'article_details.html', context)


def download_page_view(request, path):
    file_path = os.path.join(settings.MEDIA_ROOT, path)
    if os.path.exists(file_path):
        with open(file_path, 'rb') as fh:
            response = HttpResponse(
                fh.read(), content_type="application/vnd.ms-word")
            response['Content-Disposition'] = 'inline; filename=' + \
                os.path.basename(file_path)
            return response
    raise Http404


def open_xml_file(request):
    xml_file_path = 'sitemap.xml'
    xml_tree = xml.dom.minidom.parse(xml_file_path)
    return HttpResponse(xml_tree.toxml())


def read_file(request):
    file_path = 'robots.txt'

    with open(file_path, 'r') as file:
        content = file.read()
    response = HttpResponse(content, content_type='text/plain')

    return HttpResponse(response)


def archive_view(request):
    year = datetime.datetime.now().year
    years = list(range(2023, year+1))
    context = {"years": years}
    return render(request, 'arxiv.html', context)


def contact_view(request):
    year = datetime.datetime.now().year
    years = list(range(2023, year+1))
    context = {"years": years}
    return render(request, 'aloqa-uchun.html', context)


def archive_2018(request):
    year = datetime.datetime.now().year
    years = list(range(2023, year+1))
    context = {"years": years}
    return render(request, "2018.html", context)


def archive_2019(request):
    year = datetime.datetime.now().year
    years = list(range(2023, year+1))
    context = {"years": years}
    return render(request, "2019.html", context)


def archive_2020(request):
    year = datetime.datetime.now().year
    years = list(range(2023, year+1))
    context = {"years": years}
    return render(request, "2020.html", context)


def archive_2021(request):
    year = datetime.datetime.now().year
    years = list(range(2023, year+1))
    context = {"years": years}
    return render(request, "2021.html", context)


def archive_2022(request):
    year = datetime.datetime.now().year
    years = list(range(2023, year+1))
    context = {"years": years}
    return render(request, "2022.html", context)


def archive_year(request, year):
    year_num = datetime.datetime.now().year
    years = list(range(2023, year_num+1))
    choose_issues = models.Issue.objects.filter(created_at__year=year)
    context = {
        "year": year,
        "issues": choose_issues,
        "years": years
    }
    return render(request, "year_issue.html", context)
