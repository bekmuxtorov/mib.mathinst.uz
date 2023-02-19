import datetime
import os
from django.http import Http404, HttpResponse
from django.shortcuts import render

from config import settings
from . import models

# Create your views here.


def home_view(request):
    return render(request, 'index.html')


def about_view(request):
    return render(request, 'jurnal-haqida.html')


def editorial_view(request):
    return render(request, 'tahririyat.html')


def for_author_view(request):
    return render(request, 'mualliflar-uchun.html')


def last_issue_view(request):
    last_issue = models.Issue.objects.order_by("-created_at").first()
    articles = models.Article.objects.filter(issue=last_issue)
    context = {"last_issue": last_issue, "articles": articles}
    return render(request, 'oxirgi-son.html', context)


def article_detail(request, pk):
    choose_article = models.Article.objects.get(pk=pk)
    authors = choose_article.author_en.split(';')
    references = choose_article.references.split(';')
    article_value = choose_article.last_page - choose_article.first_page
    article_date = choose_article.created_at.strftime("%Y/%m/%d")
    context = {
        "article": choose_article,
        "authors": authors,
        "references": references,
        "article_value": article_value,
        "article_date": article_date,
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
    return render(request, 'arxiv.html')


def contact_view(request):
    return render(request, 'aloqa-uchun.html')


def archive_2018(request):
    return render(request, "2018.html")


def archive_2019(request):
    return render(request, "2019.html")


def archive_2020(request):
    return render(request, "2020.html")


def archive_2021(request):
    return render(request, "2021.html")


def archive_2022(request):
    return render(request, "2022.html")


def archive_year(request, year):
    choose_issues = models.Issue.objects.filter(created_at__year=year)
    context = {
        "year": year,
        "issues": choose_issues,
    }
    for issue in choose_issues:
        print(issue.id)
        articles = models.Article.objects.filter(issue=issue)
        context[f"issue_{issue.id}"] = list(articles)

    return render(request, "year_issue.html", context)
