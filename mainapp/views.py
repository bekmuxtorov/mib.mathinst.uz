import datetime
import os
from django.http import Http404, HttpResponse
from django.shortcuts import render

from config import settings
from . import models

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
    year = datetime.datetime.now().year
    years = list(range(2023, year+1))
    context = {"years": years}
    return render(request, 'mualliflar-uchun.html', context)


def last_issue_view(request):
    year = datetime.datetime.now().year
    years = list(range(2023, year+1))
    last_issue = models.Issue.objects.order_by("-created_at").first()
    articles = models.Article.objects.filter(issue=last_issue)
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
